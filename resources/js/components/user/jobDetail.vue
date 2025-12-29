<template>
    <div style="background-color: #bfbbbb;padding: 2rem;">
        <h2>Job Detail</h2>

        <Loading v-if="loading" /> 

        <div v-else>
            <div v-if="job">
               
                <h4 class="text-capitalize mb-1">{{job.title}}</h4>

                <div v-if="job.company" class="text-capitalize mb-3" style="font-size: 18px;">
                    {{job.company.name}}
                </div>

                <div class="d-flex align-items-center gap-2 mb-2" v-if="job.work_location">
                    <i class="fas fa-map-marker-alt"></i>{{ job.work_location }}
                </div>

                <div class="d-flex align-items-center gap-2 mb-2" v-if="job.specialization_name">
                    <i class="fas fa-building"></i>{{ job.specialization_name }}
                </div>

                <div v-if="job.employment_type" class="mb-2">
                    <i class="fas fa-clock"></i> {{ job.employment_type.charAt(0).toUpperCase() + job.employment_type.slice(1) }}
                </div>

                <div class="d-flex align-items-center gap-2 mb-2" v-if="job.salary_min && job.salary_max">
                    <i class="fas fa-money-bill"></i> RM {{job.salary_min}} - RM {{job.salary_max}} per month
                </div>

                <div class="mb-2 text-muted">
                    Posted {{job.created_at_human}}
                </div>

                <div class="d-flex align-items-center gap-2 mb-4">
                    <button class="btn btn-primary" @click="apply(job)" v-if="!isApplied">Apply</button>
                    <button class="btn btn-outline-primary" @click="toggleSave(job)" :disabled="isToggle">{{ job.saved_jobs && job.saved_jobs.length > 0 ? 'Unsave' : 'Save' }}</button>
                    <!-- v-if="!job.saved_jobs?.length" -->
                </div>

                <div class="mb-2" v-if="job.description">
                    <div class="fw-bold">Job Description</div>
                    <ul>
                        <li>{{job.description}}</li>
                    </ul>
                </div>

                <div class="mb-2">
                    <div class="fw-bold">Job Requirement</div>
                    <ul>
                        <li v-if="job.work_mode">Work {{job.work_mode}}</li>
                        <li v-if="job.education_level">
                            {{ getEducationName(job.education_level) }}
                            <span v-if="job.education_level !== 'none'"> or equivalent experience</span>
                         </li>
                        <li v-if="job.required_experience_years">At least {{job.required_experience_years}} years of experience</li>
                        <li>Good communication and interpersonal skills to work across departments</li>
                    </ul>
                </div>

                <!-- job environment -->
                <div v-if="job.company && job.company.image_paths && job.company.image_paths.length">
                    <img
                        v-for="(img, i) in job.company.image_paths"
                        :key="i"
                        :src="`/storage/${img}`"
                        alt="Company Image"
                        style="width: 200px; height: 200px; object-fit: cover; margin-right: 5px; border-radius: 6px;"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed, nextTick } from 'vue';
import axios from 'axios';
import {  useRouter, useRoute } from 'vue-router';
import Swal from 'sweetalert2';
import { inject } from 'vue';
import Loading from '@/components/loading.vue';

export default {
    components: { 
        Loading,
    },
    setup() {
        const job = ref([]);
        const error = ref(null);
        const router = useRouter();
        const route = useRoute();
        const loading = ref(false);
        const isToggle = ref(false);
        const isApplied  = ref(false);

        const educationLevels = [
            { id: 'none', name: 'No formal education required' },
            { id: 'spm', name: 'SPM / O-Level' },
            { id: 'diploma', name: 'Diploma / Advanced Diploma' },
            { id: 'bachelor', name: "Bachelor's Degree" },
            { id: 'master', name: "Master's Degree" },
            { id: 'phd', name: 'Doctorate (PhD)' }
        ];

        const getEducationName = (id) => {
            const level = educationLevels.find(item => item.id === id);
            return level ? level.name : id; // fallback to id if not found
        };

        const getJob = async () => {
            loading.value = true;
            const jobId = route.params.id;

            try {
                const response = await axios.get(`/api/user/getJob/${jobId}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('user_access_token')}`,
                    }
                });

                job.value = response.data.data;
                isApplied.value = response.data.is_applied;

            } catch (error) {
                error.value = error.response?.data?.message || 'Error fetching job';

                if (error.response?.status) {
                    Swal.fire({
                        title: 'Fetching job failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error',
                        allowOutsideClick: false,  
                        allowEscapeKey: false,
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            router.push({ name: 'Home' });
                        }
                    });
                }

            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            getJob();
        });

        const apply = async (job) => {
            router.push({ name: 'ApplyJob', params: { id: job.id }});
        };

        const toggleSave = async (job) => {
            if (isToggle.value) return;

            isToggle.value = true;

            const isSaved = job.saved_jobs && job.saved_jobs.length > 0;

            const endpoint = isSaved ? `/api/user/${job.id}/unsaveJob` : `/api/user/${job.id}/saveJob`;

            try {
                const response = await axios.post(endpoint, [], {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('user_access_token')}`,
                    }
                });

                if (isSaved) {
                    job.saved_jobs = [];

                    Swal.fire({
                        title: 'Unsave successfully',
                        icon: 'success',
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    });

                } else {
                    job.saved_jobs = [{}];

                    Swal.fire({
                        title: 'Save successfully',
                        icon: 'success',
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    });
                }
              
            } catch (error) {
                console.error('save failed:', error.response?.data || error.message);
                const text = isSaved ? 'Unsave' : 'Save';

                if (error.response?.status) {
                    Swal.fire({
                        title: `${text} job failed!`,
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error'
                    });
                }

            } finally {
                isToggle.value = false;
            }
        };

        return {
            job,
            error,
            getJob,
            apply,
            toggleSave,
            loading,
            getEducationName,
            isToggle,
            isApplied,
        };
    }
};
</script>

