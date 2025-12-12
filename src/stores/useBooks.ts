import { reactive, ref, computed } from 'vue';

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

// Sample initial data
const initialBooks: Book[] = [
  {
    id: 1,
    title: 'To Kill a Mockingbird',
    author: 'Harper Lee',
    isbn: '978-0-06-112008-4',
    year: 1960,
    genre: 'Fiction',
    description: 'A gripping tale of racial injustice and childhood innocence in the American South.',
    coverUrl: 'https://via.placeholder.com/150/4A90E2/FFFFFF?text=Book+1'
  },
  {
    id: 2,
    title: '1984',
    author: 'George Orwell',
    isbn: '978-0-452-28423-4',
    year: 1949,
    genre: 'Dystopian',
    description: 'A dystopian social science fiction novel and cautionary tale about totalitarianism.',
    coverUrl: 'https://via.placeholder.com/150/E94B3C/FFFFFF?text=Book+2'
  },
  {
    id: 3,
    title: 'The Great Gatsby',
    author: 'F. Scott Fitzgerald',
    isbn: '978-0-7432-7356-5',
    year: 1925,
    genre: 'Classic',
    description: 'A story of decadence and excess in the Jazz Age.',
    coverUrl: 'https://via.placeholder.com/150/6FCC76/FFFFFF?text=Book+3'
  }
];

const books = reactive<Book[]>([...initialBooks]);
const nextId = ref(4);

export function useBooks() {
  // Computed properties
  const totalBooks = computed(() => books.length);
  const genres = computed(() => [...new Set(books.map(b => b.genre))]);

  // CRUD Operations
  const addBook = (book: Omit<Book, 'id'>): Book => {
    const newBook: Book = {
      ...book,
      id: nextId.value++
    };
    books.push(newBook);
    return newBook;
  };

  const getBookById = (id: number): Book | undefined => {
    return books.find(book => book.id === id);
  };

  const updateBook = (id: number, updatedBook: Omit<Book, 'id'>): boolean => {
    const index = books.findIndex(book => book.id === id);
    if (index !== -1) {
      books[index] = { ...updatedBook, id };
      return true;
    }
    return false;
  };

  const deleteBook = (id: number): boolean => {
    const index = books.findIndex(book => book.id === id);
    if (index !== -1) {
      books.splice(index, 1);
      return true;
    }
    return false;
  };

  const getAllBooks = () => books;

  const searchBooks = (query: string): Book[] => {
    const lowerQuery = query.toLowerCase();
    return books.filter(book => 
      book.title.toLowerCase().includes(lowerQuery) ||
      book.author.toLowerCase().includes(lowerQuery) ||
      book.genre.toLowerCase().includes(lowerQuery)
    );
  };

  return {
    books,
    totalBooks,
    genres,
    addBook,
    getBookById,
    updateBook,
    deleteBook,
    getAllBooks,
    searchBooks
  };
}


