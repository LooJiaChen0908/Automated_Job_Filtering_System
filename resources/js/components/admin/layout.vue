<template>
    <div class="admin-layout">
        <AdminNavbar />
        <div class="d-flex layout-body">
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
.admin-layout {
    height: 100vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.layout-body {
    flex: 1;
    display: flex;
    overflow: hidden;
}

.sidebar {
    width: 200px;
    height: calc(100vh - 56px); /* Full height minus navbar */
    margin-top: 56px;
    position: fixed;
    padding-top: 1rem;
    background-color: #f8f9fa;
    z-index: 1000;
}

.main-content {
    margin-left: 200px;
    margin-top: 56px;
    padding: 2rem;
    height: calc(100vh - 56px);
    overflow-y: auto;
}
</style>