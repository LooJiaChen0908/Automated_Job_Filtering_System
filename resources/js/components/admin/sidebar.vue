<template>
    <div>
        <div class="sidebar bg-light">
            <ul class="nav flex-column">
                <li class="nav-item" :class="{ current: isActive('/admin') }">
                    <router-link to="/admin" class="nav-link text-dark" active-class="active">Dashboard</router-link>
                </li>
                <li class="nav-item" :class="{ current: isActive('/admin/user') }">
                    <router-link to="/admin/user" class="nav-link text-dark" active-class="active">User</router-link>
                </li>
                <li class="nav-item" :class="{ current: isActive('/admin/company') }">
                    <router-link to="/admin/company" class="nav-link text-dark" active-class="active">Company</router-link>
                </li>
                <li class="nav-item" :class="{ current: isActive('/admin/job') }">
                    <router-link to="/admin/job" class="nav-link text-dark" active-class="active">Job</router-link>
                </li>
                <li class="nav-item" :class="{ current: isActive('/admin/application') }">
                    <router-link to="/admin/application" class="nav-link text-dark" active-class="active">Application</router-link>
                </li>
                <li class="nav-item" :class="{ current: isActive('/admin/interview') }">
                    <router-link to="/admin/interview" class="nav-link text-dark" active-class="active">Interview</router-link>
                </li>
                <li class="nav-item" :class="{ current: isActive('/admin/meeting') }">
                    <router-link to="/admin/meeting" class="nav-link text-dark" active-class="active">Meeting</router-link>
                </li>
                <!-- <li class="nav-item">
                    :class="{ current: isActive('/admin/job') }"
                    <router-link to="/admin/job" class="nav-link text-dark" active-class="active">Shortlisted</router-link>
                </li> -->
                <!-- Add more items as needed -->
            </ul>
        </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()

const isActive = (path) => route.path === path

</script>

<style scoped>
.sidebar {
    width: 200px;
    position: fixed;
    height: calc(100% - 41px); /* Adjust if your navbar is taller */
    margin-top: 41px; /* Push below navbar */
    padding-top: 1rem;
    background-color: #f8f9fa; /* Optional: ensure it's visible */
    z-index: 1000; /* Ensure it stays above content if needed */
}

.current{
    background-color: #cfe2ff;
}
</style>



