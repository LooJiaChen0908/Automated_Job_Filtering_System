<template>
    <div class="layout">
      <Navbar></Navbar>
      <main class="content">
        <router-view /> <!-- This will load the page-specific content -->
      </main>
      <Footer></Footer>
    </div>
</template>

<script>
import Navbar from './navbar.vue';
import Footer from './footer.vue';

export default {
components: {
    Navbar,
    Footer,
},
};
</script>

<style scoped>
.layout{
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* .content{
    flex: 1; 
    padding: 1rem;
    background-color: #c0ffae;
} */
</style>
  