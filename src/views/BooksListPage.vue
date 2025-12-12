<template>
  <ion-page>
    <ion-content :fullscreen="true" class="library-content">
      <div class="library-container">
        <!-- Header -->
        <div class="header-bar">
          <button class="back-btn" @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </button>
          <h1 class="page-title">Library</h1>
          <button class="add-btn" @click="goToAddBook">
            <ion-icon :icon="addOutline"></ion-icon>
          </button>
        </div>

        <!-- Search Bar -->
        <div class="search-section">
          <ion-searchbar
            v-model="searchQuery"
            placeholder="Search by title, author, genre..."
            @ionInput="handleSearch"
            class="custom-searchbar"
          ></ion-searchbar>
        </div>

        <!-- Genre Filter -->
        <div class="genre-filter" v-if="availableGenres.length > 0">
          <button 
            v-for="genre in availableGenres" 
            :key="genre"
            class="genre-chip"
            :class="{ active: selectedGenre === genre }"
            @click="toggleGenreFilter(genre)"
          >
            {{ genre }}
          </button>
        </div>

        <!-- Empty State -->
        <div v-if="displayedBooks.length === 0" class="empty-state">
          <ion-icon :icon="bookOutline" class="empty-icon"></ion-icon>
          <h2>{{ searchQuery ? 'No Results Found' : 'Your Library is Empty' }}</h2>
          <p>{{ searchQuery ? 'Try adjusting your search terms' : 'Add your first book to get started' }}</p>
          <button v-if="!searchQuery" class="btn-primary" @click="goToAddBook">
            <ion-icon :icon="addOutline"></ion-icon>
            Add First Book
          </button>
        </div>

        <!-- Books Grid -->
        <div v-else class="books-grid">
          <div v-for="book in displayedBooks" :key="book.id" class="book-card">
            <!-- Book Cover (Clickable) -->
            <div class="book-cover-wrapper" @click="viewBook(book)">
              <div class="book-cover">
                <img v-if="book.coverUrl" :src="book.coverUrl" :alt="book.title" />
                <div v-else class="cover-placeholder">
                  <span>{{ book.title.charAt(0) }}</span>
                </div>
              </div>
              <!-- Quick Action Icons -->
              <div class="quick-actions">
                <button class="quick-action-btn" @click.stop="editBook(book)">
                  <ion-icon :icon="createOutline"></ion-icon>
                </button>
                <button class="quick-action-btn delete" @click.stop="confirmDelete(book)">
                  <ion-icon :icon="trashOutline"></ion-icon>
                </button>
              </div>
            </div>
            
            <!-- Book Info -->
            <div class="book-info" @click="viewBook(book)">
              <h3 class="book-title">{{ book.title }}</h3>
              <p class="book-author">{{ book.author }}</p>
              <div class="book-meta">
                <span class="genre-badge">{{ book.genre }}</span>
                <span class="year-badge">{{ book.year }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Navigation -->
      <div class="bottom-nav">
        <button class="nav-item" @click="goHome">
          <ion-icon :icon="homeOutline"></ion-icon>
          <span>Home</span>
        </button>
        <button class="nav-item active">
          <ion-icon :icon="libraryOutline"></ion-icon>
          <span>Library</span>
        </button>
        <button class="nav-item center-btn" @click="goToAddBook">
          <div class="center-icon">
            <ion-icon :icon="addOutline"></ion-icon>
          </div>
        </button>
        <button class="nav-item">
          <ion-icon :icon="searchOutline"></ion-icon>
          <span>Search</span>
        </button>
        <button class="nav-item">
          <ion-icon :icon="personOutline"></ion-icon>
          <span>Profile</span>
        </button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
  IonPage,
  IonContent,
  IonIcon,
  IonSearchbar,
  alertController,
  toastController
} from '@ionic/vue';
import {
  addOutline,
  bookOutline,
  createOutline,
  trashOutline,
  arrowBackOutline,
  homeOutline,
  libraryOutline,
  searchOutline,
  personOutline
} from 'ionicons/icons';
import { useBooks, type Book } from '@/stores/useBooks.api';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const { books, deleteBook, loadBooks } = useBooks();
const searchQuery = ref('');
const selectedGenre = ref<string | null>(null);

// Load books from database when component mounts
onMounted(async () => {
  await loadBooks();
  // Check if there's a genre filter in query params
  if (route.query.genre) {
    selectedGenre.value = route.query.genre as string;
  }
});

const availableGenres = computed(() => {
  const genreSet = new Set(books.map(b => b.genre));
  return Array.from(genreSet);
});

const displayedBooks = computed(() => {
  let filtered = books;
  
  // Filter by search query
  if (searchQuery.value.trim()) {
    const lowerQuery = searchQuery.value.toLowerCase();
    filtered = filtered.filter(book => 
      book.title.toLowerCase().includes(lowerQuery) ||
      book.author.toLowerCase().includes(lowerQuery) ||
      book.genre.toLowerCase().includes(lowerQuery)
    );
  }
  
  // Filter by selected genre
  if (selectedGenre.value) {
    filtered = filtered.filter(book => book.genre === selectedGenre.value);
  }
  
  return filtered;
});

const handleSearch = (event: CustomEvent) => {
  searchQuery.value = event.detail.value || '';
};

const toggleGenreFilter = (genre: string) => {
  if (selectedGenre.value === genre) {
    selectedGenre.value = null;
  } else {
    selectedGenre.value = genre;
  }
};

const goBack = () => {
  router.back();
};

const goHome = () => {
  router.push('/home');
};

const goToAddBook = () => {
  router.push('/books/add');
};

const viewBook = (book: Book) => {
  router.push(`/books/view/${book.id}`);
};

const editBook = (book: Book) => {
  router.push(`/books/edit/${book.id}`);
};

const confirmDelete = async (book: Book) => {
  const alert = await alertController.create({
    header: 'Delete Book',
    message: `Delete "${book.title}"?`,
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Delete',
        role: 'destructive',
        handler: () => {
          handleDelete(book.id);
        }
      }
    ]
  });

  await alert.present();
};

const handleDelete = async (id: number) => {
  const success = await deleteBook(id);
  
  const toast = await toastController.create({
    message: success ? 'Book deleted successfully' : 'Failed to delete book',
    duration: 2000,
    color: success ? 'success' : 'danger',
    position: 'bottom'
  });
  
  await toast.present();
};
</script>

<style scoped>
.library-content {
  --background: linear-gradient(180deg, #4a5568 0%, #2d3748 100%);
}

.library-container {
  padding: 0 0 80px 0;
  min-height: 100%;
}

/* Header Bar */
.header-bar {
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.back-btn, .add-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
  color: white;
  flex-shrink: 0;
}

.back-btn:hover, .add-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.back-btn ion-icon, .add-btn ion-icon {
  font-size: 24px;
}

.page-title {
  font-size: 24px;
  font-weight: 700;
  color: #ffffff;
  margin: 0;
  flex: 1;
  text-align: center;
}

/* Search Section */
.search-section {
  padding: 0 20px 16px;
}

.custom-searchbar {
  --background: rgba(255, 255, 255, 0.15);
  --border-radius: 12px;
  --box-shadow: none;
  --color: #ffffff;
  --placeholder-color: rgba(255, 255, 255, 0.6);
  --icon-color: rgba(255, 255, 255, 0.6);
  padding: 0;
}

/* Genre Filter */
.genre-filter {
  display: flex;
  gap: 8px;
  padding: 0 20px 20px;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.genre-filter::-webkit-scrollbar {
  display: none;
}

.genre-chip {
  flex-shrink: 0;
  padding: 8px 16px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: rgba(255, 255, 255, 0.8);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.genre-chip:hover {
  background: rgba(255, 255, 255, 0.15);
}

.genre-chip.active {
  background: #ffffff;
  color: #2d3748;
  border-color: #ffffff;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 24px;
  max-width: 480px;
  margin: 0 auto;
}

.empty-icon {
  font-size: 64px;
  color: rgba(255, 255, 255, 0.3);
  margin-bottom: 16px;
}

.empty-state h2 {
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #ffffff;
}

.empty-state p {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 32px 0;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 12px;
  padding: 14px 24px;
  color: white;
  font-size: 15px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: transform 0.2s;
}

.btn-primary:hover {
  transform: translateY(-2px);
}

.btn-primary ion-icon {
  font-size: 20px;
}

/* Books Grid */
.books-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 16px;
  padding: 0 20px 20px;
}

@media (max-width: 768px) {
  .books-grid {
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  }
}

/* Book Card */
.book-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.book-card:hover {
  transform: translateY(-4px);
  background: rgba(255, 255, 255, 0.08);
}

.book-cover-wrapper {
  position: relative;
  cursor: pointer;
}

.book-cover {
  width: 100%;
  aspect-ratio: 2/3;
  border-radius: 12px 12px 0 0;
  overflow: hidden;
}

.book-cover img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cover-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.cover-placeholder span {
  font-size: 48px;
  font-weight: 700;
  color: white;
}

/* Quick Actions Overlay */
.quick-actions {
  position: absolute;
  top: 8px;
  right: 8px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  opacity: 0;
  transition: opacity 0.2s;
}

.book-card:hover .quick-actions {
  opacity: 1;
}

.quick-action-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  color: #2d3748;
  backdrop-filter: blur(10px);
}

.quick-action-btn:hover {
  background: #ffffff;
  transform: scale(1.1);
}

.quick-action-btn.delete {
  color: #ef4444;
}

.quick-action-btn ion-icon {
  font-size: 18px;
}

/* Book Info */
.book-info {
  padding: 12px;
  cursor: pointer;
}

.book-title {
  font-size: 14px;
  font-weight: 600;
  color: #ffffff;
  margin: 0 0 4px 0;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-author {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 8px 0;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.book-meta {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.genre-badge, .year-badge {
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 10px;
  font-weight: 600;
}

.genre-badge {
  background: rgba(102, 126, 234, 0.3);
  color: #a5b4fc;
}

.year-badge {
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.7);
}

/* Bottom Navigation */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #2d3748;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 8px 16px 12px;
  box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.3);
  z-index: 1000;
}

.nav-item {
  background: none;
  border: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  color: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  padding: 4px 12px;
  transition: color 0.2s;
  font-size: 11px;
  font-weight: 500;
}

.nav-item ion-icon {
  font-size: 24px;
}

.nav-item.active {
  color: #ffffff;
}

.nav-item:hover {
  color: rgba(255, 255, 255, 0.8);
}

.center-btn {
  margin-top: -20px;
}

.center-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.center-icon ion-icon {
  font-size: 28px;
  color: white;
}
</style>

