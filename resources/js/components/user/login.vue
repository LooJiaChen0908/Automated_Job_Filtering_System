<template>
     <div class="d-flex justify-content-center flex-column align-items-center" style="padding: 10rem;background-color: skyblue;"> 
       <h3 class="text-center mb-0">Welcome User!</h3>
        <div class="d-flex flex-column m-4 rounded-1 p-5 gap-3" style="background-color: white;">
            <!-- my-2 mx-2 -->
            <h3 class="text-center">Login your account</h3>

            <div>
                <div class="input-group">
                    <span class="input-group-text" id="username">Username</span>
                    <input type="text" class="form-control" aria-describedby="username" v-model="form.username">
                </div>
                <span v-if="validationErrors.username" class="text-danger">{{ validationErrors.username[0] }}</span>
            </div>
           
            <div>
                <div class="input-group">
                    <span class="input-group-text" id="password">Password</span>
                    <input :type="isShowPassword ? 'text' : 'password'" class="form-control" aria-describedby="password" v-model="form.password">
                    <span class="input-group-text" @click="triggerPassword"><i :class="isShowPassword ? 'fas fa-eye' : 'fas fa-eye-slash'"></i></span>
                </div>
                <span v-if="validationErrors.password" class="text-danger">{{ validationErrors.password[0] }}</span>
            </div>
          
            <button class="btn btn-primary w-100" @click="login" :disabled="isSubmit">Login</button>

            <span>Don't have an account? <router-link to="/user/register" class="text-decoration-none">Sign Up now!</router-link></span>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import userApi from '@/services/userApi';

export default {
    name: 'Login',
    setup() {
        const isShowPassword = ref(false);

        const validationErrors = reactive({});

        const router = useRouter();

        const form = reactive({
            username: '',
            password: '',
        });

        const triggerPassword = () => {
            isShowPassword.value = !isShowPassword.value;
        };

        const isSubmit = ref(false);
       
        const login = async () => {
            if (isSubmit.value) return;
            
            isSubmit.value = true;

            try {
                const response = await userApi.post('/login', form);
                console.log('login success:', response.data);

                localStorage.setItem('user_access_token', response.data.access_token);
                localStorage.setItem('user_data', JSON.stringify(response.data.user));
                
                Swal.fire({
                    title: 'Login successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                router.push({
                    name: 'Home',
                });

            } catch (error) {
                console.error('Register failed:', error.response?.data || error.message);

                if (error.response?.status === 422) {
                    Object.keys(validationErrors).forEach(key => delete validationErrors[key]); // clear old errors
                    Object.assign(validationErrors, error.response.data.errors);
                } else if (error.response?.status === 401) {
                    Swal.fire({
                        title: 'Login failed!',
                        text: 'Invalid username or password.',
                        icon: 'error'
                    });
                } else {
                    Swal.fire({
                        title: 'Login failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error'
                    });
                }

            } finally {
                isSubmit.value = false;
            }
        };

        onMounted(() => {
           //
        });

        return {
            form,
            login,
            isShowPassword,
            triggerPassword,
            validationErrors,
            isSubmit
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>

