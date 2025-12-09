<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Admin System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- <li class="nav-item me-2">
                        <a class="nav-link" href="#">Profile</a>
                    </li> -->
                    <li class="nav-item d-flex">
                        <div class="btn-logout">
                            <a class="nav-link d-flex align-items-center gap-2" href="#" @click="logout">
                                <span>Log Out</span>
                                <div class="circle-wrapper">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                            </a>
                        </div>
                    </li>
                </ul>
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

export default {
    name: 'AdminNavbar',
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
                    await axios.post('/api/admin/logout', {}, {
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
                        name: 'AdminLogin',
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
  .btn-logout{
    border-bottom-right-radius: 20px;
    border-top-right-radius: 20px;
    border-top-left-radius: 20px;
    border-bottom-left-radius: 20px;
    background-color: lightgray;
    color: white;
  }


  .navbar{
    border-bottom: 1px solid lightgray;
  }
</style>
  