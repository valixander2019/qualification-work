<template>
  <div class="main-layout h-100 d-flex flex-column">
    <header>
      <navbar />
    </header>

    <main class="content container mt-4">
      <child />
    </main>

    <hr>

    <div style="background-color: white;">
      <footer class="container flex-shrink-0 pt-3">
        <p>
          2022 © Malik
        </p>
      </footer>
    </div>
  </div>
</template>

<script>
import Navbar from '~/components/admin/Navbar'

export default {
  name: 'AdminLayout',

  components: {
    Navbar
  }
}
</script>
