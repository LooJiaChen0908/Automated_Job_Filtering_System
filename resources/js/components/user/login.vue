<template>
     <div class="d-flex justify-content-center flex-column align-items-center" style="padding: 10rem;background-color: skyblue;"> 
       <h3 class="text-center mb-0">Welcome User!</h3>
        <div class="d-flex flex-column m-4 rounded-1 p-5 " style="background-color: white;">
            <!-- my-2 mx-2 -->
            <h3 class="text-center">Login your account</h3>

            <div class="input-group mb-3">
                <span class="input-group-text" id="username">Username</span>
                <input type="text" class="form-control" aria-describedby="username" v-model="form.username">
                <span v-if="validationErrors.username" class="text-danger">{{ validationErrors.username[0] }}</span>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text" id="password">Password</span>
                <input :type="isShowPassword ? 'text' : 'password'" class="form-control" aria-describedby="password" v-model="form.password">
                <span class="input-group-text" @click="triggerPassword"><i :class="isShowPassword ? 'fas fa-eye' : 'fas fa-eye-slash'"></i></span>
                <span v-if="validationErrors.password" class="text-danger">{{ validationErrors.password[0] }}</span>
            </div>
            <button class="btn btn-primary w-100" @click="login" @disabled="isSubmit">Login</button>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

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

            // router.push({
            //     name: 'Home',
            // });

            // return
            if (isSubmit.value) return;
            
            isSubmit.value = true;

            try {
                const response = await axios.post('/api/user/login', form);
                console.log('login success:', response.data);

                localStorage.setItem('access_token', response.data.access_token);
                localStorage.setItem('user_data', JSON.stringify(response.data.user));
                
                Swal.fire({
                    title: 'Login successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;

                router.push({
                    name: 'Home',
                });

            } catch (error) {
                console.error('Register failed:', error.response?.data || error.message);
                alert('login failed!');
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
            validationErrors
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>

