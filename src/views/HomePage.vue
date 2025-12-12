<template>
  <ion-page>
    <ion-content :fullscreen="true" class="home-content">
      <div class="home-container">
        <!-- Header with Profile -->
        <div class="header-section">
          <div class="profile-header">
            <div class="profile-info">
              <div class="avatar">
                <ion-icon :icon="personOutline"></ion-icon>
              </div>
              <h1 class="greeting">Hello User!</h1>
            </div>
            <ion-button fill="clear" class="notification-btn">
              <ion-icon :icon="notificationsOutline"></ion-icon>
            </ion-button>
          </div>

          <!-- Search Bar -->
          <div class="search-container">
            <ion-searchbar
              v-model="searchQuery"
              placeholder="Search by Book title, Author, etc"
              @ionFocus="goToBooks"
              class="custom-searchbar"
            ></ion-searchbar>
          </div>
        </div>

        <!-- Top Available Books -->
        <div class="section">
          <div class="section-header">
            <h2 class="section-title">Top Available for you!</h2>
            <button class="view-all-btn" @click="goToBooks">View All</button>
          </div>
          
          <div class="books-horizontal-scroll">
            <div 
              v-for="book in topBooks" 
              :key="book.id" 
              class="book-card-horizontal"
              @click="viewBook(book)"
            >
              <div class="book-cover-small">
                <img v-if="book.coverUrl" :src="book.coverUrl" :alt="book.title" />
                <div v-else class="cover-placeholder-small">
                  <span>{{ book.title.charAt(0) }}</span>
                </div>
                <div class="book-author-overlay">{{ book.author.split(' ').pop() }}</div>
              </div>
            </div>
            <div class="add-book-card" @click="goToAddBook">
              <ion-icon :icon="addOutline"></ion-icon>
              <span>Add New</span>
            </div>
          </div>
        </div>

        <!-- Popular Genres -->
        <div class="section">
          <div class="section-header">
            <h2 class="section-title">Pick from Popular Genres</h2>
            <button class="view-all-btn" @click="goToBooks">View All</button>
          </div>
          
          <div class="genres-grid">
            <div 
              v-for="(genre, index) in popularGenres" 
              :key="genre"
              class="genre-card"
              :style="{ background: genreColors[index % genreColors.length] }"
              @click="filterByGenre(genre)"
            >
              <ion-icon :icon="getGenreIcon(genre)"></ion-icon>
              <span>{{ genre }}</span>
            </div>
          </div>
        </div>

        <!-- Recent Books -->
        <div class="section">
          <div class="section-header">
            <h2 class="section-title">Your Recently Added Books</h2>
            <button class="view-all-btn" @click="goToBooks">View All</button>
          </div>
          
          <div class="books-horizontal-scroll">
            <div 
              v-for="book in recentBooks" 
              :key="book.id" 
              class="book-card-vertical"
              @click="viewBook(book)"
            >
              <div class="book-cover-medium">
                <img v-if="book.coverUrl" :src="book.coverUrl" :alt="book.title" />
                <div v-else class="cover-placeholder-medium">
                  <span>{{ book.title.charAt(0) }}</span>
                </div>
              </div>
              <div class="book-info-compact">
                <div class="book-year-badge">{{ book.year }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Navigation -->
      <div class="bottom-nav">
        <button class="nav-item active">
          <ion-icon :icon="homeOutline"></ion-icon>
          <span>Home</span>
        </button>
        <button class="nav-item" @click="goToBooks">
          <ion-icon :icon="libraryOutline"></ion-icon>
          <span>Library</span>
        </button>
        <button class="nav-item center-btn" @click="goToAddBook">
          <div class="center-icon">
            <ion-icon :icon="addOutline"></ion-icon>
          </div>
        </button>
        <button class="nav-item" @click="goToBooks">
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
  IonButton,
  IonSearchbar
} from '@ionic/vue';
import {
  bookOutline,
  libraryOutline,
  addOutline,
  searchOutline,
  personOutline,
  notificationsOutline,
  homeOutline,
  flaskOutline,
  schoolOutline,
  heartOutline,
  rocketOutline,
  brushOutline,
  planetOutline
} from 'ionicons/icons';
import { useBooks, type Book } from '@/stores/useBooks.api';
import { useRouter } from 'vue-router';

const router = useRouter();
const { books, loadBooks } = useBooks();
const searchQuery = ref('');

// Load books from database when component mounts
onMounted(async () => {
  await loadBooks();
});

// Computed properties for different book sections
const topBooks = computed(() => books.slice(0, 6));
const recentBooks = computed(() => [...books].reverse().slice(0, 4));
const popularGenres = computed(() => {
  const genreSet = new Set(books.map(b => b.genre));
  return Array.from(genreSet).slice(0, 4);
});

const genreColors = [
  'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
  'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
  'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
  'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
  'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
  'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
  'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
  'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)'
];

const getGenreIcon = (genre: string) => {
  const iconMap: Record<string, string> = {
    'Fiction': bookOutline,
    'Science': flaskOutline,
    'History': schoolOutline,
    'Romance': heartOutline,
    'Sci-Fi': rocketOutline,
    'Art': brushOutline,
    'Fantasy': planetOutline,
    'Dystopian': rocketOutline,
    'Classic': bookOutline
  };
  return iconMap[genre] || bookOutline;
};

const goToBooks = () => {
  router.push('/books');
};

const goToAddBook = () => {
  router.push('/books/add');
};

const viewBook = (book: Book) => {
  router.push(`/books/view/${book.id}`);
};

const filterByGenre = (genre: string) => {
  router.push({ path: '/books', query: { genre } });
};
</script>

<style scoped>
.home-content {
  --background: linear-gradient(180deg, #4a5568 0%, #2d3748 100%);
}

.home-container {
  padding: 0 0 80px 0;
  min-height: 100%;
}

/* Header Section */
.header-section {
  padding: 20px 20px 24px;
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.profile-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.avatar ion-icon {
  font-size: 24px;
}

.greeting {
  font-size: 18px;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
}

.notification-btn {
  --color: #ffffff;
  font-size: 24px;
}

/* Search Container */
.search-container {
  margin-top: 16px;
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

/* Section Styles */
.section {
  margin-bottom: 32px;
  padding: 0 20px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
}

.view-all-btn {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.7);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  padding: 4px 8px;
}

/* Horizontal Scroll for Books */
.books-horizontal-scroll {
  display: flex;
  gap: 16px;
  overflow-x: auto;
  padding-bottom: 8px;
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.books-horizontal-scroll::-webkit-scrollbar {
  display: none;
}

/* Small Book Cards (Top Available) */
.book-card-horizontal {
  flex-shrink: 0;
  width: 120px;
  cursor: pointer;
  transition: transform 0.2s;
}

.book-card-horizontal:hover {
  transform: translateY(-4px);
}

.book-cover-small {
  width: 120px;
  height: 160px;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.book-cover-small img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cover-placeholder-small {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.cover-placeholder-small span {
  font-size: 48px;
  font-weight: 700;
  color: white;
}

.book-author-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
  color: white;
  padding: 8px;
  font-size: 11px;
  font-weight: 500;
  text-transform: uppercase;
}

/* Add Book Card */
.add-book-card {
  flex-shrink: 0;
  width: 120px;
  height: 160px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.1);
  border: 2px dashed rgba(255, 255, 255, 0.3);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
  color: rgba(255, 255, 255, 0.7);
}

.add-book-card:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.5);
  color: white;
}

.add-book-card ion-icon {
  font-size: 32px;
}

.add-book-card span {
  font-size: 13px;
  font-weight: 500;
}

/* Genres Grid */
.genres-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.genre-card {
  aspect-ratio: 1;
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: transform 0.2s;
  color: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.genre-card:hover {
  transform: translateY(-4px);
}

.genre-card ion-icon {
  font-size: 32px;
}

.genre-card span {
  font-size: 12px;
  font-weight: 600;
  text-align: center;
}

/* Medium Book Cards (Recent Books) */
.book-card-vertical {
  flex-shrink: 0;
  width: 110px;
  cursor: pointer;
  transition: transform 0.2s;
}

.book-card-vertical:hover {
  transform: translateY(-4px);
}

.book-cover-medium {
  width: 110px;
  height: 150px;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  margin-bottom: 8px;
}

.book-cover-medium img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cover-placeholder-medium {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.cover-placeholder-medium span {
  font-size: 48px;
  font-weight: 700;
  color: white;
}

.book-info-compact {
  display: flex;
  align-items: center;
  justify-content: center;
}

.book-year-badge {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
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

@media (max-width: 768px) {
  .genres-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}
</style>
