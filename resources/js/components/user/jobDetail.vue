<template>
    <div v-if="job" style="background-color: #bfbbbb;padding: 2rem;">
        <h2>Job Detail</h2>

        <h4 class="text-capitalize">{{job.title}}</h4>

        <div>
            {{job.work_mode}}
        </div>

        <div v-if="job.employment_type">
            <i class="fas fa-clock"></i> {{ job.employment_type.charAt(0).toUpperCase() + job.employment_type.slice(1) }}
        </div>

        <div class="mb-2">
            Posted {{job.created_at_human}}
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary" @click="apply(job)">Apply</button>
            <button class="btn btn-outline-primary" @click="toggleSave(job)">{{ job.saved_jobs && job.saved_jobs.length > 0 ? 'Unsave' : 'Save' }}</button>
            <!-- v-if="!job.saved_jobs?.length" -->
        </div>

        <div class="mb-2">
            Job Summary<br>
            {{job.description}}
        </div>

        <div v-if="job.company && job.company.image_paths && job.company.image_paths.length">
            <img
                v-for="(img, i) in job.company.image_paths"
                :key="i"
                :src="`/storage/${img}`"
                alt="Company Image"
                style="width: 250px; height: 250px; object-fit: cover; margin-right: 5px; border-radius: 6px;"
            />
        </div>

    </div>
</template>

<script>
import { ref, onMounted, computed, nextTick } from 'vue';
import axios from 'axios';
import {  useRouter, useRoute } from 'vue-router';
import Swal from 'sweetalert2';
import { inject } from 'vue';

export default {
    setup() {
        const job = ref([]);
        const error = ref(null);
        const router = useRouter();
        const route = useRoute();

        const getJob = async () => {
            const jobId = route.params.id;
            try {
                const response = await axios.get(`/api/user/getJob/${jobId}`);
                job.value = response.data.data;
            } catch (err) {
                error.value = err.response?.data?.message || 'Error fetching job';
            }
        };

        onMounted(() => {
            getJob();
        });

        const apply = async (job) => {
            router.push({ name: 'ApplyJob', params: { id: job.id }});
        };

        const toggleSave = async (job) => {
            const isSaved = job.saved_jobs && job.saved_jobs.length > 0;

            const endpoint = isSaved ? `/api/user/${job.id}/unsaveJob` : `/api/user/${job.id}/saveJob`;

            try {
                const response = await axios.post(endpoint, [], {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                    }
                });

                if (isSaved) {
                    job.saved_jobs = [];
                    // alert('unsave successful!');
                    Swal.fire({
                        title: 'Unsave successfully',
                        icon: 'success',
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    });

                } else {
                    job.saved_jobs = [{}];
                    // alert('save successful!');

                    Swal.fire({
                        title: 'Save successfully',
                        icon: 'success',
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    });
                }
              
            } catch (error) {
                console.error('save failed:', error.response?.data || error.message);
                alert('save failed!');
            }
        };

        return {
            job,
            error,
            getJob,
            apply,
            toggleSave,
        };
    }
};
</script>

