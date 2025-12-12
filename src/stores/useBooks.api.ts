import { reactive, ref, computed } from 'vue';
import bookService from '@/services/bookService';

export interface Book {
  id: number;
  title: string;
  author: string;
  isbn: string;
  year: number;
  genre: string;
  description: string;
  coverUrl?: string;
}

const books = reactive<Book[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

export function useBooks() {
  // Computed properties
  const totalBooks = computed(() => books.length);
  const genres = computed(() => [...new Set(books.map(b => b.genre))]);

  // Load all books from API
  const loadBooks = async () => {
    loading.value = true;
    error.value = null;
    
    try {
      const data = await bookService.getAllBooks();
      books.splice(0, books.length, ...data);
    } catch (err) {
      error.value = 'Failed to load books';
      console.error(err);
    } finally {
      loading.value = false;
    }
  };

  // CRUD Operations with API
  const addBook = async (book: Omit<Book, 'id'>): Promise<Book | null> => {
    loading.value = true;
    error.value = null;
    
    try {
      const newBook = await bookService.createBook(book);
      books.push(newBook);
      return newBook;
    } catch (err) {
      error.value = 'Failed to add book';
      console.error(err);
      return null;
    } finally {
      loading.value = false;
    }
  };

  const getBookById = (id: number): Book | undefined => {
    return books.find(book => book.id === id);
  };

  const updateBook = async (id: number, updatedBook: Omit<Book, 'id'>): Promise<boolean> => {
    loading.value = true;
    error.value = null;
    
    try {
      const updated = await bookService.updateBook(id, updatedBook);
      const index = books.findIndex(book => book.id === id);
      if (index !== -1) {
        books[index] = updated;
        return true;
      }
      return false;
    } catch (err) {
      error.value = 'Failed to update book';
      console.error(err);
      return false;
    } finally {
      loading.value = false;
    }
  };

  const deleteBook = async (id: number): Promise<boolean> => {
    loading.value = true;
    error.value = null;
    
    try {
      await bookService.deleteBook(id);
      const index = books.findIndex(book => book.id === id);
      if (index !== -1) {
        books.splice(index, 1);
        return true;
      }
      return false;
    } catch (err) {
      error.value = 'Failed to delete book';
      console.error(err);
      return false;
    } finally {
      loading.value = false;
    }
  };

  const getAllBooks = () => books;

  const searchBooks = async (query: string): Promise<Book[]> => {
    if (!query.trim()) return books;
    
    loading.value = true;
    error.value = null;
    
    try {
      return await bookService.searchBooks(query);
    } catch (err) {
      error.value = 'Failed to search books';
      console.error(err);
      // Fallback to local search
      const lowerQuery = query.toLowerCase();
      return books.filter(book => 
        book.title.toLowerCase().includes(lowerQuery) ||
        book.author.toLowerCase().includes(lowerQuery) ||
        book.genre.toLowerCase().includes(lowerQuery)
      );
    } finally {
      loading.value = false;
    }
  };

  return {
    books,
    loading,
    error,
    totalBooks,
    genres,
    loadBooks,
    addBook,
    getBookById,
    updateBook,
    deleteBook,
    getAllBooks,
    searchBooks
  };
}


