import api from './api';
import { Book } from '@/stores/useBooks';

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}

export interface PaginatedResponse<T> {
  success: boolean;
  data: T[];
  pagination: {
    total: number;
    page: number;
    limit: number;
    totalPages: number;
  };
}

class BookService {
  // PHP endpoints
  private endpoint = '/books/read.php';
  private readSingleEndpoint = '/books/read_single.php';
  private createEndpoint = '/books/create.php';
  private updateEndpoint = '/books/update.php';
  private deleteEndpoint = '/books/delete.php';

  /**
   * Get all books
   */
  async getAllBooks(): Promise<Book[]> {
    try {
      const response = await api.get<ApiResponse<Book[]>>(this.endpoint);
      return response.data;
    } catch (error) {
      console.error('Error fetching books:', error);
      throw error;
    }
  }

  /**
   * Get paginated books (same as getAllBooks for PHP version)
   */
  async getBooks(page = 1, limit = 10): Promise<PaginatedResponse<Book>> {
    try {
      const response = await api.get<ApiResponse<Book[]>>(this.endpoint);
      return {
        success: response.success,
        data: response.data,
        pagination: {
          total: response.data.length,
          page: 1,
          limit: response.data.length,
          totalPages: 1
        }
      };
    } catch (error) {
      console.error('Error fetching paginated books:', error);
      throw error;
    }
  }

  /**
   * Get a single book by ID
   */
  async getBookById(id: number): Promise<Book> {
    try {
      const response = await api.get<ApiResponse<Book>>(`${this.readSingleEndpoint}?id=${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching book ${id}:`, error);
      throw error;
    }
  }

  /**
   * Create a new book
   */
  async createBook(book: Omit<Book, 'id'>): Promise<Book> {
    try {
      const response = await api.post<ApiResponse<Book>>(this.createEndpoint, book);
      return response.data;
    } catch (error) {
      console.error('Error creating book:', error);
      throw error;
    }
  }

  /**
   * Update an existing book
   */
  async updateBook(id: number, book: Omit<Book, 'id'>): Promise<Book> {
    try {
      const response = await api.put<ApiResponse<Book>>(`${this.updateEndpoint}?id=${id}`, book);
      return response.data;
    } catch (error) {
      console.error(`Error updating book ${id}:`, error);
      throw error;
    }
  }

  /**
   * Delete a book
   */
  async deleteBook(id: number): Promise<void> {
    try {
      await api.delete<ApiResponse<void>>(`${this.deleteEndpoint}?id=${id}`);
    } catch (error) {
      console.error(`Error deleting book ${id}:`, error);
      throw error;
    }
  }

  /**
   * Search books
   * Note: PHP version does server-side filtering
   */
  async searchBooks(query: string): Promise<Book[]> {
    try {
      // For PHP version, we'll get all books and filter client-side
      // You can implement server-side search in PHP if needed
      const response = await api.get<ApiResponse<Book[]>>(this.endpoint);
      const allBooks = response.data;
      
      const lowerQuery = query.toLowerCase();
      return allBooks.filter(book => 
        book.title.toLowerCase().includes(lowerQuery) ||
        book.author.toLowerCase().includes(lowerQuery) ||
        book.genre.toLowerCase().includes(lowerQuery)
      );
    } catch (error) {
      console.error('Error searching books:', error);
      throw error;
    }
  }

  /**
   * Filter books by genre
   */
  async getBooksByGenre(genre: string): Promise<Book[]> {
    try {
      const response = await api.get<ApiResponse<Book[]>>(this.endpoint);
      const allBooks = response.data;
      return allBooks.filter(book => book.genre === genre);
    } catch (error) {
      console.error('Error fetching books by genre:', error);
      throw error;
    }
  }

  /**
   * Upload book cover image
   */
  async uploadCover(bookId: number, file: File): Promise<string> {
    try {
      const formData = new FormData();
      formData.append('cover', file);

      const response = await api.post<ApiResponse<{ url: string }>>(
        `${this.endpoint}/${bookId}/cover`,
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        }
      );
      return response.data.url;
    } catch (error) {
      console.error('Error uploading cover:', error);
      throw error;
    }
  }
}

export default new BookService();

