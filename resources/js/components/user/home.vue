<template>
    <div>
        <div class="job-search-section py-5 bg-sky" v-if="!loading">
            <div class="container text-center">
                <div class="search-bar d-flex flex-wrap justify-content-center align-items-end gap-4">
                    <div class="search-field text-start">
                        <label class="search-label">What</label>
                        <input
                            type="text"
                            placeholder="Enter keywords"
                            v-model="form.keyword"
                            @keyup.enter="search"
                            class="form-control search-input"
                        />
                    </div>

                    <div class="search-field text-start">
                        <label class="search-label">Work Type</label>
                        <v-select
                            :options="employment_types"
                            label="name"
                            :reduce="type => type.id"
                            v-model="form.employment_type"
                            placeholder="Select work type"
                            class="search-select"
                        ></v-select>
                    </div>

                    <div class="search-field text-start">
                        <label class="search-label">Work Mode</label>
                        <v-select
                            :options="['On-site', 'Remote', 'Hybrid']"
                            v-model="form.work_mode"
                            placeholder="Select work mode"
                            class="search-select"
                        ></v-select>
                    </div>

                    <div class="search-field">
                        <button class="btn btn-primary search-btn" @click="search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    
        <Loading v-if="loading" /> 

        <div v-if="jobs.length" style="background-color: #bfbbbb;" class="px-3 py-3">
            <div>
                <h3 class="mb-4">Recommendations</h3>

                <!-- <p class="text-muted">4565</p> -->

                <div v-for="job in jobs" :key="job.id" class="mb-3" style="background-color: white; border-radius: 10px;">
                    <div class="ms-3" @click="viewDetail(job)">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <b class="text-capitalize">{{job.title}} - {{job.company ? job.company.state : '-'}}</b>
                            <span class="badge bg-danger" v-if="isNew(job.created_at)">NEW</span>
                        </div>
                
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-map-marker-alt"></i>{{ job.work_location }}
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-1" v-if="job.specialization_name">
                            <i class="fas fa-building"></i>{{ job.specialization_name }}
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-1" v-if="job.employment_type">
                            <i class="fas fa-clock"></i> {{ job.employment_type.charAt(0).toUpperCase() + job.employment_type.slice(1) }}
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-money-bill"></i> RM {{job.salary_min}} - RM {{job.salary_max}}
                        </div>
                            
                        <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
                            <span class="text-muted">{{job.created_at_human}}</span>
                            <i 
                                :class="[job.saved_jobs && job.saved_jobs.length > 0 ? 'fas fa-bookmark text-primary' : 'far fa-bookmark']" 
                                class="me-2 cursor-pointer" 
                                @click.stop="triggerSave(job)" 
                                :title="job.saved_jobs && job.saved_jobs.length > 0 ? 'Unsave' : 'Save'"
                                data-toggle="tooltip" data-placement="top"
                            ></i>

                        </div>
                    </div>
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
    name: 'Home',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const jobs = ref([]);
        const loading = ref(false);
        const search_item = ref(null);

        const form = reactive({
            keyword: '',
            type: '',
            employment_type: '',
            work_mode: '',
        });

        const employment_types = ref([
            { id: 'full-time', name: 'Full-time' },
            { id: 'part-time', name: 'Part-time' },
            { id: 'temporary', name: 'Temporary' },
            { id: 'internship', name: 'Internship' },
        ]);

        const isNew = (createdAt) => {
            const jobDate = new Date(createdAt);
            const now = new Date();

            const diffInMs = now - jobDate; // difference in milliseconds
            const diffInDays = diffInMs / (1000 * 60 * 60 * 24);

            return diffInDays < 3; // true if created within 2 days
        };

        const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/user/getAvailableJobs');
                jobs.value = response.data.jobs;
            } catch (error) {
                console.error("Error fetching jobs:", error);
            } finally {
                loading.value = false;
            }
        };

        const triggerSave = async (job) => {
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

        onMounted(getData);

        const search = async () => {
            // loading.value = true;

            try {
                const response = await axios.get('/api/user/searchJob', {
                    params: {
                        keyword: form.keyword,
                        employment_type: form.employment_type,
                        work_mode: form.work_mode,
                    }
                });

                jobs.value = response.data.jobs;

                // if (!jobs.value.length) {
                //     Swal.fire('No results found', '', 'info');
                // }

            } catch (error) {
                console.error('Search failed:', error.response?.data || error.message);
                Swal.fire('Search failed', '', 'error');
            }
           

        };

        const viewDetail = async (job) => {
            router.push({ name: 'JobDetail', params: { id: job.id} });
        };

        return {
           jobs,
           loading,
           form,
           search,
           viewDetail,
           triggerSave,
           isNew,
           search_item,
           employment_types,
        };
    }
};
</script>

<style scoped>
    .bg-sky {
        background-color: #87CEEB;
    }

    .search-bar {
        flex-wrap: wrap;
    }

    .search-field {
        display: flex;
        flex-direction: column;
    }

    .search-label {
        font-weight: 600;
        margin-bottom: 6px;
    }

    .search-input,
    .search-select {
        width: 220px;
        border-radius: 10px;
        background-color: #fff;
        border: none;
    }

    @media (max-width: 768px) {
        .search-input,
        .search-select {
            width: 100%;
        }
    }

    .cursor-pointer{
        cursor: pointer;
    }
</style>
