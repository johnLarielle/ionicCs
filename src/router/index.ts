import { createRouter, createWebHistory } from '@ionic/vue-router';
import { RouteRecordRaw } from 'vue-router';
import HomePage from '../views/HomePage.vue'
import BooksListPage from '../views/BooksListPage.vue'
import BookFormPage from '../views/BookFormPage.vue'
import BookViewPage from '../views/BookViewPage.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: '/home'
  },
  {
    path: '/home',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/books',
    name: 'BooksList',
    component: BooksListPage
  },
  {
    path: '/books/add',
    name: 'AddBook',
    component: BookFormPage
  },
  {
    path: '/books/edit/:id',
    name: 'EditBook',
    component: BookFormPage
  },
  {
    path: '/books/view/:id',
    name: 'ViewBook',
    component: BookViewPage
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
