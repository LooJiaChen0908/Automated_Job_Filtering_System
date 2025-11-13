<template>
    <div>
        <AdminNavbar />
        <div class="d-flex">
            <AdminSidebar />
            
            <div class="main-content flex-fill">
                <router-view></router-view> <!-- This is where each page's content will load -->
            </div>
        </div>
    </div>
</template>


<script>
import AdminNavbar from './navbar.vue';
import AdminSidebar from './sidebar.vue';
  
export default {
components: {
    AdminNavbar,
    AdminSidebar,
}
};
</script>

<style scoped>
.sidebar {
    width: 250px;
    position: fixed;
    height: calc(100% - 56px); /* Account for fixed navbar height */
    margin-top: 56px; /* Space for navbar */
    padding-top: 1rem;
}

.main-content {
    margin-left: 250px; /* Sidebar width */
    margin-top: 56px; /* Space for navbar */
    padding: 2rem;
}
</style>