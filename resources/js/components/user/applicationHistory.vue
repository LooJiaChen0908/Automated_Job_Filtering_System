<template>
    <div class="p-5"  style="background-color: #bfbbbb;">
        <h2>Application History</h2>

        Saved | Applied

        <Loading v-if="loading" />

        <div v-else>
            <div v-if="saved_jobs.length">
                <span class="mb-2">Saved Jobs ({{countSavedJobs}} jobs)</span>
                <!-- <span class="badge badge-pill bg-warning">{{countSavedJobs}}</span> -->

                <div v-for="s_job in saved_jobs" :key="s_job.id" class="card mb-2">
                    <div class="card-body">
                        <div class="text-capitalize mb-2">
                            <b>{{s_job.job.title}}</b>
                            <div>{{s_job.job.company.name}}</div>
                        </div>

                        <div v-if="s_job.job.company" class="mb-2">
                            {{s_job.job.company.city}}, {{s_job.job.company.state}}
                        </div>

                        <div v-if="s_job.job.salary_min" class="mb-2">
                            RM {{s_job.job.salary_min}} - RM {{s_job.job.salary_max}}
                        </div>

                        <span class="text-muted mb-2">{{s_job.job.description}}</span>

                        <div class="text-muted mb-2">Posted {{s_job.job_created_at_human}}</div>

                        <div v-if="s_job.job.status != 0">
                            <button class="btn btn-primary" @click="apply(s_job)">Apply</button>
                        </div>
                    </div>
                    <div class="card-footer text-muted" v-if="s_job.job.status == 0">
                        This job is no longer active
                    </div>
                </div>
            </div>
            <div v-else class="d-flex flex-column">
                <i class="fas fa-bookmark" style="font-size: 3rem;"></i>
                No jobs yet
            </div>

            <div v-if="applied_jobs.length">
                Applied Job

                <div class="card" v-for="a_job in applied_jobs" :key="a_job.id">
                    <div class="card-body">
                        <div class="text-capitalize mb-2">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <b>{{a_job.job.title}}</b>
                                <span class="badge bg-warning" v-if="a_job.status == 0">Pending</span>
                                <span class="badge bg-success" v-else="a_job.status == 1">Approved</span>
                            </div>
                        
                            <div>{{a_job.job.company.name}}</div>
                        </div>

                        <div v-if="a_job.job.company" class="mb-2">
                            {{a_job.job.company.city}}, {{a_job.job.company.state}}
                        </div>

                        <div v-if="a_job.job.salary_min" class="mb-2">
                            RM {{a_job.job.salary_min}} - RM {{a_job.job.salary_max}}
                        </div>
                    
                        <span class="text-muted">{{a_job.job.description}}</span>
                    </div>
                </div>
            </div>
            <div v-else class="d-flex flex-column">
                <i class="fas fa-briefcase" style="font-size: 3rem;"></i>
                No applications yet
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
    name: 'ApplicationHistory',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const saved_jobs = ref([]);
        const applied_jobs = ref([]);
        const loading = ref(false);
        const search_item = ref(null);
        const countSavedJobs = ref(null);
        const currentTab = ref(null);

        const getData = async () => {
            loading.value = true;
            try {
                // const response = await axios.get(`/api/user/getAppliedJob?currentTab=${currentTab}`);
              
                const response = await axios.get(`/api/user/getAppliedJob?currentTab=${currentTab}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                    }
                });

                saved_jobs.value = response.data.savedJobs;
                applied_jobs.value = response.data.appliedJobs;
                countSavedJobs.value = response.data.countSavedJobs;

            } catch (error) {
                console.error("Error fetching jobs:", error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(getData);


        const triggerTab = async () => {

        };

        const apply = async (job) => {

        };
      
        return {
           saved_jobs,
           applied_jobs,
           loading,
           countSavedJobs,
           apply,
           currentTab,
        };
    }
};
</script>

<style scoped>
    
</style>
