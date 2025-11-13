<template>
    <div>
        <div class="sidebar bg-light mt-5">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <router-link to="/admin" class="nav-link text-dark" active-class="active">Dashboard</router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/admin/company" class="nav-link text-dark" active-class="active">Company</router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/admin/job" class="nav-link text-dark" active-class="active">Job</router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/admin/application" class="nav-link text-dark" active-class="active">Application</router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/admin/job" class="nav-link text-dark" active-class="active">Shortlisted</router-link>
                </li>
                <!-- Add more items as needed -->
            </ul>
        </div>
    </div>
</template>

<script>
export default {
name: 'AdminSidebar',

};
</script>

<style scoped>
.sidebar {
width: 250px;
position: fixed;
height: 100%;
}

.current{
background-color: lavender;
}
</style>



