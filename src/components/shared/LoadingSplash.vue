<template>
  <div class="loading-splash">
    <div class="content-wrapper">
      <SuiProgressSpinner :size="36" />
      <p v-if="message" class="loading-text">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import SuiProgressSpinner from '@/components/studip/SuiProgressSpinner.vue';

defineProps({
  message: {
    type: String,
    default: 'Daten werden geladen...'
  }
});
</script>

<style lang="scss" scoped>
.loading-splash {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(2px);
  z-index: 100;
  border-radius: inherit;
  animation: fadeIn 0.2s ease-in-out;
  color: #28497c;

  .content-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    text-align: center;
  }

  .loading-text {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 500;
    color: #676767;
    font-family: sans-serif;
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>