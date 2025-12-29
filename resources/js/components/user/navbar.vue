<template>
    <div>
        <nav class="navbar navbar-expand-lg" style="background-color: #4d77d9;">
            <div class="container-fluid" style="color: white;">
                <router-link to="/user/home" class="navbar-brand text-decoration-none text-white">Automated Job Filtering System</router-link> 
                <!-- jom apply -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarNav">
                    <ul class="navbar-nav">
                        <!-- <li class="nav-item">
                            <a class="nav-link text-white active" aria-current="page" href="#">Home</a>
                        </li> -->
                        <!-- <li class="nav-item">
                            <router-link to="/user/applicationHistory" class="nav-link text-white">Application History</router-link>
                        </li> -->
                        <li class="nav-item">
                            <router-link to="" class="nav-link text-white">Service</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="" class="nav-link text-white">About Us</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="" class="nav-link text-white">Contact Us</router-link>
                        </li>
                    </ul>
                    
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group" v-if="userData">
                            <button type="button" class="btn btn-light dropdown-toggle text-capitalize" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i>&nbsp;{{userData.name}}
                            </button>
                            <ul class="dropdown-menu">
                                <li><router-link to="/user/profile" class="dropdown-item">Profile</router-link></li>
                                <li><router-link :to="{ path: '/user/applicationHistory', query: { type: 'saved' } }" class="dropdown-item">Saved Job</router-link></li>
                                <li><router-link :to="{ path: '/user/applicationHistory', query: { type: 'applied' } }" class="dropdown-item">Applied Job</router-link></li>
                            </ul>
                        </div>
                        <button class="btn btn-light" @click="logout">Log Out <i class="fas fa-sign-out-alt"></i></button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import { inject } from 'vue';

export default {
setup(){
    const is_login = ref(false);
    const router = useRouter();
    const userData = ref(null);

    const logout = async () => {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'You want to logout!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            try {
                await axios.post('/api/user/logout', {}, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                    }
                });

                ['access_token', 'user_data'].forEach(item => {
                    if (localStorage.getItem(item)) {
                        localStorage.removeItem(item);
                    }
                });

                router.push({
                    name: 'Login',
                });

                await Swal.fire({
                    title: 'Logout successful',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

            } catch (error) {
                console.error('Logout failed:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Logout failed. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Okay'
                });
            }
        }
    };

    onMounted(async () => {
        const stored = localStorage.getItem('user_data');
        if(stored){
            userData.value = JSON.parse(stored);
            // should use applicant's name instead of user name
        }
    });

    return {
        is_login,
        logout,
        userData,
    };
}
}; 
</script>

<style scoped>
    .btn-sign-in{
        background-color: lightgray;
        border: white;
        color: black;
    }

    .container-fluid{
        color: white;
    }

    .dropdown-item:active,
    .dropdown-item:focus {
        background-color: #4d77d9 !important;
        color: white !important;              
    }

    .dropdown-item:hover {
        background-color: #4d77d9 !important;
        color: white !important;
    }

   .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1); /* subtle white overlay */
        color: #ffffff;
        border-radius: 4px;
    }
</style>