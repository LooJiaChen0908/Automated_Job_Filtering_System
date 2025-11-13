<template>
    <div>
        <div class="p-5">

            <h2>Applying for {{job.title}}</h2>

            {{job}}

            Upload resume

            <input type="file" class="form-control" @change="handleFileChange($event, 'resume')" accept=".pdf,.doc,.docx">

            upload cover letter

            <input type="file" class="form-control" @change="handleFileChange($event, 'cover')" accept=".pdf,.doc,.docx">


            <div class="d-flex justify-content-end mt-2">
                <button class="btn btn-primary" @click="submit">Submit</button>
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
            try {
                const response = await axios.get(`/api/admin/job/getJob/${props.id}`)

                job.value = response.data.job;
            } catch (error) {
                console.error("There was an error fetching job:", error);
            }
        };

        onMounted(fetchJob);

        const handleFileChange = (event, type) => {
            const file = event.target.files[0]
            if (type === 'resume') form.resume = file
            if (type === 'cover') form.cover_letter = file
        }

        const submit = async () => {

            if (!form.resume) {
                alert('Please upload your resume before submitting.')
                return
            }

            // if(!name){
            //     alert
            //     router.push({ name: 'Profile' });
            // }

            // complete profile first

            

            const formData = new FormData()
            formData.append('job_id', props.id)
            formData.append('resume', form.resume)
            if (form.cover_letter) formData.append('cover_letter', form.cover_letter)

            try {
                const response = await axios.post('/api/user/applyJob', formData, {
                    headers: { 
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                    }
                });

                console.log('success:', response.data);

                // router.push({ name: 'Home' });

            } catch (error) {
                console.error('Apply failed:', error.response?.data || error.message);
                alert('failed!');
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


