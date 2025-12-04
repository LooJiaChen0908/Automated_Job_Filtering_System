<template>
    <div class="d-flex justify-content-center flex-column align-items-center" style="padding: 10rem; background-color: skyblue;">
        <div class="d-flex flex-column rounded-1 p-5 gap-4" style="background-color: white; width: 35%;">
            <h3 class="text-center mb-0">Register your account</h3>

            <div>
                <input type="text" class="form-control" v-model="form.username" placeholder="Username" />
                <span v-if="validationErrors.username" class="text-danger validation-error">{{ validationErrors.username[0] }}</span>
            </div>

            <div>
                <input type="text" class="form-control" v-model="form.email" placeholder="Email" />
                <span v-if="validationErrors.email" class="text-danger validation-error">{{ validationErrors.email[0] }}</span>
            </div>
            
            <div>
                <div class="input-group">
                    <input :type="isShowPassword ? 'text' : 'password'" class="form-control" v-model="form.password" placeholder="Password" /> 
                    <label class="input-group-text trigger-password" @click="triggerPassword"><i :class="isShowPassword ? 'fas fa-eye' : 'fas fa-eye-slash'"></i></label>
                </div>
                <span v-if="validationErrors.password" class="text-danger validation-error">{{ validationErrors.password[0] }}</span>
            </div>

            <div>
                <div class="input-group">
                    <input :type="isShowConfirmedPassword ? 'text' : 'password'" class="form-control" v-model="form.confirmedPassword" placeholder="Confirmed Password" /> 
                    <label class="input-group-text trigger-password" @click="triggerConfirmedPassword"><i :class="isShowConfirmedPassword ? 'fas fa-eye' : 'fas fa-eye-slash'"></i></label>
                </div>
                <span v-if="validationErrors.confirmedPassword" class="text-danger validation-error">{{ validationErrors.confirmedPassword[0] }}</span>
            </div>
            
            <button class="btn btn-primary btn-register w-100" @click="register" :disabled="isSubmit">Register</button>

            <p class="mb-0">Already have an account? <router-link to="/user/login" class="text-decoration-none">Login here</router-link></p>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    name: 'Register',
    setup() {
        const isShowPassword = ref(false);
        const isShowConfirmedPassword = ref(false);
        const router = useRouter();
        const isSubmit = ref(false);

        const form = reactive({
            username: '',
            email: '',
            role: 'user',
            password: '',
            confirmedPassword: null
        });

        const validationErrors = reactive({});

        const triggerPassword = () => {
            isShowPassword.value = !isShowPassword.value;
        };

        const triggerConfirmedPassword = () => {
            isShowConfirmedPassword.value = !isShowConfirmedPassword.value;
        };

        const register = async () => {
            if (isSubmit.value) return;

            isSubmit.value = true;

            try {
                const response = await axios.post('/api/user/register', form);
                console.log('Register success:', response.data);

                Swal.fire({
                    title: 'Registration successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                router.push({
                    name: 'Login',
                });
              
            } catch (error) {
                console.error('Register failed:', error.response?.data || error.message);

                if (error.response?.status === 422) {
                    Object.assign(validationErrors, error.response.data.errors);
                } else {
                    Swal.fire({
                        title: 'Registration failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error'
                    });
                }

            } finally {
                isSubmit.value = false;
            }
        };

        onMounted(async () => {
            //
        });

        return {
            form,
            register,
            isShowPassword,
            isShowConfirmedPassword,
            validationErrors,
            triggerPassword,
            triggerConfirmedPassword,
            isSubmit
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
