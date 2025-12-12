<template>
  <ion-app>
    <ion-router-outlet />
  </ion-app>
</template>

<script setup lang="ts">
import { IonApp, IonRouterOutlet } from '@ionic/vue';
</script>

<style>
/* Modern Global Styles */
* {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

body {
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

/* Global content background - override per page as needed */
ion-content {
  --background: #f8fafc;
}

/* Card styles */
ion-card {
  margin: 0;
  border-radius: 16px;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  --background: #ffffff;
}

/* Button styles */
ion-button {
  text-transform: none;
  font-weight: 600;
  letter-spacing: 0;
  --border-radius: 12px;
  height: 48px;
  font-size: 15px;
}

ion-button::part(native) {
  padding: 0 24px;
}

/* Toolbar styles */
ion-toolbar {
  --background: #ffffff;
  --border-color: #e2e8f0;
  --color: #1e293b;
  --min-height: 56px;
}

ion-title {
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -0.02em;
}

/* Searchbar styles */
ion-searchbar {
  --background: #f1f5f9;
  --border-radius: 12px;
  --box-shadow: none;
  --icon-color: #64748b;
  --placeholder-color: #94a3b8;
  --color: #1e293b;
  padding: 8px;
}

/* Chip styles */
ion-chip {
  --background: #f1f5f9;
  --color: #475569;
  border-radius: 8px;
  font-weight: 500;
  height: 32px;
  font-size: 13px;
}

/* Item styles */
ion-item {
  --border-color: #e2e8f0;
  --background: transparent;
  --padding-start: 0;
  --inner-padding-end: 0;
}

ion-list {
  background: transparent;
  padding: 0;
}

/* Input, textarea, select styles */
ion-input,
ion-textarea,
ion-select {
  --padding-start: 16px;
  --padding-end: 16px;
  --padding-top: 12px;
  --padding-bottom: 12px;
  --background: #f8fafc;
  --border-radius: 12px;
  --color: #1e293b;
  --placeholder-color: #94a3b8;
  font-size: 15px;
}

/* Back button */
ion-back-button {
  --color: #64748b;
}

/* Spinner */
ion-spinner {
  --color: #667eea;
}

/* Alert customization */
.alert-wrapper {
  border-radius: 16px !important;
}

.alert-button {
  font-weight: 600 !important;
}

/* Toast customization */
.toast-wrapper {
  border-radius: 12px !important;
}

/* Scrollbar styling for webkit browsers */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.3);
}

/* Animation utilities */
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.fade-in {
  animation: fadeIn 0.3s ease-in;
}

.slide-up {
  animation: slideUp 0.4s ease-out;
}

/* Ensure smooth transitions */
* {
  -webkit-tap-highlight-color: transparent;
}

button {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/* Remove default button focus outline, add custom */
button:focus-visible {
  outline: 2px solid #667eea;
  outline-offset: 2px;
}
</style>
