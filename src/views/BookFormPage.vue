<template>
  <ion-page>
    <ion-content :fullscreen="true" class="form-content">
      <div class="form-wrapper">
        <!-- Header -->
        <div class="header-bar">
          <button class="back-btn" @click="handleCancel" type="button">
            <ion-icon :icon="arrowBackOutline"></ion-icon>
          </button>
          <h1 class="page-title">{{ isEditMode ? 'Edit Book' : 'New Book' }}</h1>
          <div class="placeholder"></div>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="form-container">
            <div class="form-section">
              <h2 class="section-title">Book Information</h2>
            
            <div class="form-grid">
              <div class="form-field full-width">
                <label class="field-label">Title</label>
                <ion-input
                  v-model="formData.title"
                  placeholder="Enter book title"
                  required
                  :maxlength="100"
                  class="modern-input"
                ></ion-input>
              </div>

              <div class="form-field full-width">
                <label class="field-label">Author</label>
                <ion-input
                  v-model="formData.author"
                  placeholder="Enter author name"
                  required
                  class="modern-input"
                ></ion-input>
              </div>

              <div class="form-field">
                <label class="field-label">ISBN</label>
                <ion-input
                  v-model="formData.isbn"
                  placeholder="978-0-123456-78-9"
                  required
                  class="modern-input"
                ></ion-input>
              </div>

              <div class="form-field">
                <label class="field-label">Publication Year</label>
                <ion-input
                  v-model.number="formData.year"
                  type="number"
                  placeholder="2024"
                  :min="1000"
                  :max="currentYear"
                  required
                  class="modern-input"
                ></ion-input>
              </div>

              <div class="form-field full-width">
                <label class="field-label">Genre</label>
                <ion-select
                  v-model="formData.genre"
                  placeholder="Select a genre"
                  interface="action-sheet"
                  required
                  class="modern-select"
                >
                  <ion-select-option value="Fiction">Fiction</ion-select-option>
                  <ion-select-option value="Non-Fiction">Non-Fiction</ion-select-option>
                  <ion-select-option value="Mystery">Mystery</ion-select-option>
                  <ion-select-option value="Science Fiction">Science Fiction</ion-select-option>
                  <ion-select-option value="Fantasy">Fantasy</ion-select-option>
                  <ion-select-option value="Romance">Romance</ion-select-option>
                  <ion-select-option value="Thriller">Thriller</ion-select-option>
                  <ion-select-option value="Biography">Biography</ion-select-option>
                  <ion-select-option value="History">History</ion-select-option>
                  <ion-select-option value="Self-Help">Self-Help</ion-select-option>
                  <ion-select-option value="Classic">Classic</ion-select-option>
                  <ion-select-option value="Dystopian">Dystopian</ion-select-option>
                  <ion-select-option value="Other">Other</ion-select-option>
                </ion-select>
              </div>

              <div class="form-field full-width">
                <label class="field-label">Description</label>
                <ion-textarea
                  v-model="formData.description"
                  placeholder="Enter a brief description of the book"
                  :auto-grow="true"
                  :rows="4"
                  :maxlength="500"
                  required
                  class="modern-textarea"
                ></ion-textarea>
                <span class="char-count">{{ formData.description.length }}/500</span>
              </div>

              <div class="form-field full-width">
                <label class="field-label">Cover Image URL <span class="optional">(Optional)</span></label>
                <ion-input
                  v-model="formData.coverUrl"
                  placeholder="https://example.com/cover.jpg"
                  type="url"
                  class="modern-input"
                ></ion-input>
              </div>
            </div>
          </div>

          <!-- Image Preview -->
          <div v-if="formData.coverUrl" class="preview-section">
            <h3 class="preview-title">Cover Preview</h3>
            <div class="cover-preview">
              <img :src="formData.coverUrl" alt="Cover preview" @error="handleImageError" />
            </div>
          </div>

            <div class="button-group">
              <button type="submit" class="btn-primary" :disabled="!isFormValid">
                <ion-icon :icon="isEditMode ? saveOutline : addOutline"></ion-icon>
                {{ isEditMode ? 'Save Changes' : 'Add Book' }}
              </button>
              
              <button type="button" class="btn-secondary" @click="handleCancel">
                Cancel
              </button>
            </div>
          </div>
        </form>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
  IonPage,
  IonContent,
  IonInput,
  IonTextarea,
  IonSelect,
  IonSelectOption,
  IonIcon,
  toastController
} from '@ionic/vue';
import {
  arrowBackOutline,
  addOutline,
  saveOutline
} from 'ionicons/icons';
import { useBooks } from '@/stores/useBooks.api';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const { addBook, updateBook, getBookById } = useBooks();

const currentYear = new Date().getFullYear();
const isEditMode = computed(() => route.name === 'EditBook');
const bookId = computed(() => route.params.id ? parseInt(route.params.id as string) : null);

const formData = ref({
  title: '',
  author: '',
  isbn: '',
  year: currentYear,
  genre: '',
  description: '',
  coverUrl: ''
});

const isFormValid = computed(() => {
  return (
    formData.value.title.trim() !== '' &&
    formData.value.author.trim() !== '' &&
    formData.value.isbn.trim() !== '' &&
    formData.value.year >= 1000 &&
    formData.value.year <= currentYear &&
    formData.value.genre !== '' &&
    formData.value.description.trim() !== ''
  );
});

onMounted(() => {
  if (isEditMode.value && bookId.value) {
    const book = getBookById(bookId.value);
    if (book) {
      formData.value = {
        title: book.title,
        author: book.author,
        isbn: book.isbn,
        year: book.year,
        genre: book.genre,
        description: book.description,
        coverUrl: book.coverUrl || ''
      };
    } else {
      showToast('Book not found', 'danger');
      router.push('/books');
    }
  }
});

const handleSubmit = async () => {
  if (!isFormValid.value) {
    showToast('Please fill in all required fields', 'warning');
    return;
  }

  const bookData = {
    title: formData.value.title.trim(),
    author: formData.value.author.trim(),
    isbn: formData.value.isbn.trim(),
    year: formData.value.year,
    genre: formData.value.genre,
    description: formData.value.description.trim(),
    coverUrl: formData.value.coverUrl.trim() || undefined
  };

  if (isEditMode.value && bookId.value) {
    const success = await updateBook(bookId.value, bookData);
    if (success) {
      showToast('Book updated successfully', 'success');
      router.push('/books');
    } else {
      showToast('Failed to update book', 'danger');
    }
  } else {
    const result = await addBook(bookData);
    if (result) {
      showToast('Book added successfully', 'success');
      router.push('/books');
    } else {
      showToast('Failed to add book', 'danger');
    }
  }
};

const handleCancel = () => {
  router.back();
};

const handleImageError = () => {
  showToast('Failed to load image', 'warning');
};

const showToast = async (message: string, color: string = 'primary') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'bottom'
  });
  await toast.present();
};
</script>

<style scoped>
.form-content {
  --background: linear-gradient(180deg, #4a5568 0%, #2d3748 100%);
}

.form-wrapper {
  min-height: 100%;
  padding-bottom: 24px;
}

/* Header Bar */
.header-bar {
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
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
  flex-shrink: 0;
}

.back-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.back-btn ion-icon {
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

.placeholder {
  width: 40px;
  flex-shrink: 0;
}

.form-container {
  background: #ffffff;
  border-radius: 24px 24px 0 0;
  padding: 24px 20px;
  margin-top: 8px;
  min-height: calc(100vh - 80px);
}

/* Form Section */
.form-section {
  margin-bottom: 24px;
}

.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 20px 0;
  letter-spacing: -0.01em;
}

/* Form Grid */
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-field.full-width {
  grid-column: 1 / -1;
}

.field-label {
  font-size: 14px;
  font-weight: 600;
  color: #475569;
  margin: 0;
}

@media (prefers-color-scheme: dark) {
  .field-label {
    color: #cbd5e1;
  }
}

.optional {
  font-weight: 400;
  color: #94a3b8;
  font-size: 13px;
}

.char-count {
  font-size: 12px;
  color: #94a3b8;
  text-align: right;
  margin-top: 4px;
}

/* Modern Inputs */
.modern-input,
.modern-textarea,
.modern-select {
  --background: #f8fafc;
  --border-radius: 12px;
  --padding-start: 16px;
  --padding-end: 16px;
  --padding-top: 14px;
  --padding-bottom: 14px;
  --placeholder-color: #94a3b8;
  --placeholder-opacity: 1;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 15px;
  transition: all 0.2s;
}

@media (prefers-color-scheme: dark) {
  .modern-input,
  .modern-textarea,
  .modern-select {
    --background: #1e293b;
    border-color: #334155;
  }
}

.modern-input:focus-within,
.modern-textarea:focus-within,
.modern-select:focus-within {
  border-color: #2563eb;
  --background: #ffffff;
}

@media (prefers-color-scheme: dark) {
  .modern-input:focus-within,
  .modern-textarea:focus-within,
  .modern-select:focus-within {
    border-color: #3b82f6;
    --background: #0f172a;
  }
}

.modern-textarea {
  --padding-top: 12px;
  --padding-bottom: 12px;
  min-height: 120px;
}

/* Preview Section */
.preview-section {
  background: #f8fafc;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 32px;
}

@media (prefers-color-scheme: dark) {
  .preview-section {
    background: #1e293b;
  }
}

.preview-title {
  font-size: 16px;
  font-weight: 600;
  color: #475569;
  margin: 0 0 16px 0;
}

@media (prefers-color-scheme: dark) {
  .preview-title {
    color: #cbd5e1;
  }
}

.cover-preview {
  width: 200px;
  height: 267px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  background: #ffffff;
}

@media (prefers-color-scheme: dark) {
  .cover-preview {
    border-color: #334155;
    background: #0f172a;
  }
}

.cover-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Button Group */
.button-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding-top: 24px;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 12px;
  padding: 16px 24px;
  color: white;
  font-size: 16px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: transform 0.2s, opacity 0.2s;
  width: 100%;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
}

.btn-primary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary ion-icon {
  font-size: 20px;
}

.btn-secondary {
  background: transparent;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px 24px;
  color: #64748b;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  width: 100%;
}

.btn-secondary:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: #475569;
}

@media (max-width: 768px) {
  .form-container {
    padding: 20px 16px;
  }
  
  .form-section {
    margin-bottom: 20px;
  }
  
  .section-title {
    font-size: 16px;
  }
}
</style>

