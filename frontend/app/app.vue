<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <!-- Global Confirm Dialog -->
    <ClientOnly>
      <ConfirmDialog />
    </ClientOnly>
  </div>
</template>
