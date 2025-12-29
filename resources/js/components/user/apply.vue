<template>
    <div style="background-color: #bfbbbb;">
        <div class="p-5">
            <Loading v-if="loading" />

            <div v-else>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="text-muted">
                            Applying for
                        </div>

                        <h2 class="text-capitalize">{{job.title}}</h2>

                        <div v-if="job.company" class="text-capitalize mb-2">
                            {{job.company.name}}
                        </div>

                        <div v-if="job.description" class="text-muted">
                            Description: {{job.description}}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="mb-2">Resume</label>

                    <input type="file" class="form-control" @change="handleFileChange($event, 'resume')" accept=".pdf,.doc,.docx">

                    <!-- upload cover letter

                    <input type="file" class="form-control" @change="handleFileChange($event, 'cover')" accept=".pdf,.doc,.docx"> -->
                </div>
            
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary" @click="submit" :disabled="isSubmit">Submit</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import Loading from '@/components/loading.vue';

export default {
    props: ['id'],
    components: {
        Loading
    },
    setup(props) {
        const loading = ref(true);
        const form = reactive({ 
            name: '',
            resume: '',
            cover_letter: '',
        });
        const errors = reactive({});
        const isSubmit = ref(false);
        const router = useRouter();
        const job = ref([]);

        const fetchJob = async () => {
            loading.value = true;
            try {
                const response = await axios.get(`/api/admin/job/getJob/${props.id}`)

                job.value = response.data.job;
            } catch (error) {
                console.error("There was an error fetching job:", error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(fetchJob);

        const handleFileChange = (event, type) => {
            const file = event.target.files[0]
            if (type === 'resume') form.resume = file
            if (type === 'cover') form.cover_letter = file
        }

        const submit = async () => {
            if (isSubmit.value) return;

            if (!form.resume) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please upload your resume before submitting.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Okay'
                });

                return;
            }

            // if(!name){
            //     alert
            //     router.push({ name: 'Profile' });
            // }

            // complete profile first

            isSubmit.value = true;

            const formData = new FormData()
            formData.append('job_id', props.id)
            formData.append('resume', form.resume)
            if (form.cover_letter) formData.append('cover_letter', form.cover_letter)

            try {
                const response = await axios.post('/api/user/applyJob', formData, {
                    headers: { 
                        Authorization: `Bearer ${localStorage.getItem('user_access_token')}`,
                    }
                });
                
                Swal.fire({
                    title: 'Submitted successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                router.push({ name: 'Home' });

            } catch (error) {
                console.error('Apply failed:', error.response?.data || error.message);
                
                if (error.response?.status) {
                    Swal.fire({
                        title: 'Apply job failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error'
                    });
                }

            } finally {
                isSubmit.value = false;
            }
        };

        return {
            loading,
            form,
            errors,
            isSubmit,
            submit,
            handleFileChange,
            job,
        };
    }
};
</script>

<style scoped>
/* Add styles as needed */
</style>


