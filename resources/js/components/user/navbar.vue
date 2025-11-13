<template>
    <div>
        <nav class="navbar navbar-expand-lg" style="background-color: #4d77d9;">
            <div class="container-fluid" style="color: white;">
                <router-link to="/user/home" class="navbar-brand text-decoration-none text-dark">Automated Job Filtering System</router-link> 
                <!-- jom apply -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarNav">
                    <ul class="navbar-nav" >
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <router-link to="/user/applicationHistory" class="nav-link text-decoration-none text-dark">Application History</router-link>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact Us</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                More
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center gap-2">
                        <!-- dropdown -->
                        <router-link to="/user/profile" class="btn btn-light text-capitalize"><i class="fas fa-user"></i> <span v-if="userData">{{userData.name}}</span></router-link>
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
                    name: 'Register',
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
    /*  */
  .btn-sign-in{
    background-color: lightgray;
    border: white;
    color: black;
  }


  .container-fluid{
    color: white;
  }
</style>