<template>
  <ion-page>
    <ion-content :fullscreen="true" class="book-detail-content">
      <div v-if="book" class="book-detail-container">
        <!-- Header with Back Button -->
        <div class="header-bar">
          <button class="back-btn" @click="goBack">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </button>
        </div>

        <!-- Book Cover and Info Section -->
        <div class="hero-section">
          <div class="book-cover-large">
            <img v-if="book.coverUrl" :src="book.coverUrl" :alt="book.title" />
            <div v-else class="cover-placeholder">
              <span>{{ book.title.charAt(0) }}</span>
            </div>
            <div class="author-badge">{{ book.author.split(' ').pop() }}</div>
          </div>

          <div class="book-header-info">
            <h1 class="book-title">{{ book.title }}</h1>
            <p class="book-author">By {{ book.author }}</p>
            <div class="rating-section">
              <div class="stars">
                <ion-icon v-for="i in 5" :key="i" :icon="star" class="star-icon"></ion-icon>
              </div>
              <span class="rating-text">4.2 (by 22k users)</span>
            </div>
            <p class="books-available">{{ Math.floor(Math.random() * 10) + 1 }} Books left</p>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button class="btn-primary" @click="editBook">
              <ion-icon :icon="createOutline"></ion-icon>
              Edit Book
            </button>
            <button class="btn-icon" @click="confirmDelete">
              <ion-icon :icon="trashOutline"></ion-icon>
            </button>
          </div>
        </div>

        <!-- Content Card -->
        <div class="content-card">
          <!-- Synopsis Section -->
          <div class="section">
            <h2 class="section-title">Synopsis</h2>
            <div class="tags">
              <span class="tag">{{ book.genre }}</span>
              <span class="tag">{{ book.year }}</span>
              <span class="tag">{{ book.isbn }}</span>
            </div>
            <p class="synopsis-text">{{ book.description }}</p>
          </div>

          <!-- Similar Books Section -->
          <div class="section">
            <div class="section-header">
              <h2 class="section-title">Similar Books</h2>
              <button class="view-all-link" @click="filterByGenre">View All</button>
            </div>
            <div class="similar-books">
              <div 
                v-for="similarBook in similarBooks" 
                :key="similarBook.id"
                class="similar-book-card"
                @click="viewSimilarBook(similarBook)"
              >
                <div class="similar-cover">
                  <img v-if="similarBook.coverUrl" :src="similarBook.coverUrl" :alt="similarBook.title" />
                  <div v-else class="similar-placeholder">
                    <span>{{ similarBook.title.charAt(0) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Details Section -->
          <div class="section">
            <h2 class="section-title">Book Details</h2>
            <div class="details-list">
              <div class="detail-row">
                <span class="detail-label">Author</span>
                <span class="detail-value">{{ book.author }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">ISBN</span>
                <span class="detail-value">{{ book.isbn }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Year</span>
                <span class="detail-value">{{ book.year }}</span>
              </div>
              <div class="detail-row">
                <span class="detail-label">Genre</span>
                <span class="detail-value">{{ book.genre }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="error-state">
        <ion-icon :icon="alertCircleOutline" class="error-icon"></ion-icon>
        <h2>Book Not Found</h2>
        <p>This book doesn't exist in your library</p>
        <button class="btn-primary" @click="goToBooks">Back to Library</button>
      </div>

      <!-- Bottom Navigation -->
      <div class="bottom-nav">
        <button class="nav-item" @click="goHome">
          <ion-icon :icon="homeOutline"></ion-icon>
          <span>Home</span>
        </button>
        <button class="nav-item center-btn" @click="goToAddBook">
          <div class="center-icon">
            <ion-icon :icon="addOutline"></ion-icon>
          </div>
        </button>
        <button class="nav-item" @click="goToBooks">
          <ion-icon :icon="libraryOutline"></ion-icon>
          <span>Library</span>
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
  alertController,
  toastController
} from '@ionic/vue';
import {
  arrowBackOutline,
  createOutline,
  trashOutline,
  alertCircleOutline,
  star,
  homeOutline,
  libraryOutline,
  addOutline
} from 'ionicons/icons';
import { useBooks, type Book } from '@/stores/useBooks.api';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const { books, getBookById, deleteBook, loadBooks } = useBooks();
const book = ref<Book | undefined>();

onMounted(async () => {
  // Load books from database first
  await loadBooks();
  const bookId = parseInt(route.params.id as string);
  book.value = getBookById(bookId);
});

// Get similar books (same genre, excluding current book)
const similarBooks = computed(() => {
  if (!book.value) return [];
  return books
    .filter(b => b.genre === book.value?.genre && b.id !== book.value?.id)
    .slice(0, 4);
});

const goBack = () => {
  router.back();
};

const goHome = () => {
  router.push('/home');
};

const goToBooks = () => {
  router.push('/books');
};

const goToAddBook = () => {
  router.push('/books/add');
};

const editBook = () => {
  if (book.value) {
    router.push(`/books/edit/${book.value.id}`);
  }
};

const viewSimilarBook = (similarBook: Book) => {
  router.push(`/books/view/${similarBook.id}`);
};

const filterByGenre = () => {
  if (book.value) {
    router.push({ path: '/books', query: { genre: book.value.genre } });
  }
};

const confirmDelete = async () => {
  if (!book.value) return;

  const alert = await alertController.create({
    header: 'Delete Book',
    message: `Are you sure you want to delete "${book.value.title}"?`,
    buttons: [
      {
        text: 'Cancel',
        role: 'cancel'
      },
      {
        text: 'Delete',
        role: 'destructive',
        handler: () => {
          handleDelete();
        }
      }
    ]
  });

  await alert.present();
};

const handleDelete = async () => {
  if (!book.value) return;

  const success = await deleteBook(book.value.id);
  
  const toast = await toastController.create({
    message: success ? 'Book deleted successfully' : 'Failed to delete book',
    duration: 2000,
    color: success ? 'success' : 'danger',
    position: 'bottom'
  });
  
  await toast.present();
  
  if (success) {
    router.push('/books');
  }
};
</script>

<style scoped>
.book-detail-content {
  --background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
}

.book-detail-container {
  padding: 0 0 80px 0;
  min-height: 100%;
}

/* Header Bar */
.header-bar {
  padding: 16px 20px;
}

.back-btn {
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
}

.back-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.back-btn ion-icon {
  font-size: 24px;
}

/* Hero Section */
.hero-section {
  padding: 0 20px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.book-cover-large {
  width: 180px;
  height: 240px;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
  margin-bottom: 20px;
}

.book-cover-large img {
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
  font-size: 72px;
  font-weight: 700;
  color: white;
}

.author-badge {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
  color: white;
  padding: 8px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  text-align: center;
}

.book-header-info {
  text-align: center;
  margin-bottom: 20px;
}

.book-title {
  font-size: 22px;
  font-weight: 700;
  color: #ffffff;
  margin: 0 0 8px 0;
  line-height: 1.3;
}

.book-author {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0 0 12px 0;
}

.rating-section {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 8px;
}

.stars {
  display: flex;
  gap: 2px;
}

.star-icon {
  font-size: 16px;
  color: #fbbf24;
}

.rating-text {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.6);
}

.books-available {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 12px;
  width: 100%;
}

.btn-primary {
  flex: 1;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  border: none;
  border-radius: 12px;
  padding: 14px 24px;
  color: white;
  font-size: 15px;
  font-weight: 600;
  display: flex;
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

.btn-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.1);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
  color: white;
}

.btn-icon:hover {
  background: rgba(255, 255, 255, 0.2);
}

.btn-icon ion-icon {
  font-size: 22px;
}

/* Content Card */
.content-card {
  background: #ffffff;
  border-radius: 24px 24px 0 0;
  padding: 24px 20px 20px;
  margin-top: 24px;
}

.section {
  margin-bottom: 28px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.section-title {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 12px 0;
}

.view-all-link {
  background: none;
  border: none;
  color: #2563eb;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

/* Tags */
.tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}

.tag {
  background: #f1f5f9;
  color: #475569;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
}

.synopsis-text {
  font-size: 14px;
  line-height: 1.7;
  color: #64748b;
  margin: 0;
}

/* Similar Books */
.similar-books {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
  padding-bottom: 8px;
}

.similar-books::-webkit-scrollbar {
  display: none;
}

.similar-book-card {
  flex-shrink: 0;
  cursor: pointer;
  transition: transform 0.2s;
}

.similar-book-card:hover {
  transform: translateY(-4px);
}

.similar-cover {
  width: 90px;
  height: 120px;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.similar-cover img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.similar-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.similar-placeholder span {
  font-size: 36px;
  font-weight: 700;
  color: white;
}

/* Details List */
.details-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f1f5f9;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
}

.detail-value {
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
}

/* Error State */
.error-state {
  text-align: center;
  padding: 80px 24px;
  max-width: 480px;
  margin: 0 auto;
}

.error-icon {
  font-size: 64px;
  color: rgba(255, 255, 255, 0.3);
  margin-bottom: 16px;
}

.error-state h2 {
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #ffffff;
}

.error-state p {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.6);
  margin: 0 0 32px 0;
}

/* Bottom Navigation */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #ffffff;
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 8px 16px 12px;
  box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.nav-item {
  background: none;
  border: none;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px 12px;
  transition: color 0.2s;
  font-size: 11px;
  font-weight: 500;
}

.nav-item ion-icon {
  font-size: 24px;
}

.nav-item:hover {
  color: #64748b;
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

