<template>
    <div class="p-5" style="background-color: #bfbbbb;">
        <ul class="nav nav-tabs mb-3 custom-tabs">
            <li class="nav-item">
                <router-link :to="{ name: 'ApplicationHistory', query: { type: 'saved' } }" class="nav-link" :class="{ active: currentTab === 'saved' }">
                    <i class="far fa-bookmark"></i>&nbsp;Saved Jobs
                </router-link>
            </li>
            <li class="nav-item ms-4">
                <router-link :to="{ name: 'ApplicationHistory', query: { type: 'applied' } }" class="nav-link" :class="{ active: currentTab === 'applied' }">
                    <i class="fas fa-briefcase"></i>&nbsp;Applied Jobs
                </router-link>
            </li>
        </ul>

        <Loading v-if="loading" />

        <div v-else>
            <div v-if="currentTab === 'saved'">
                <div v-if="saved_jobs.length">
                    <div class="mb-2" v-if="countSavedJobs">{{countSavedJobs}} jobs</div>

                    <div v-for="s_job in saved_jobs" :key="s_job.id" class="card mb-2">
                        <div class="card-body">
                            <div class="text-capitalize mb-2">
                                <u><b>{{s_job.job.title}}</b></u>
                                <div v-if="s_job.job.company">{{s_job.job.company.name}}</div>
                            </div>

                            <div v-if="s_job.job.company" class="mb-2">
                                {{s_job.job.company.city}}, {{s_job.job.company.state}}
                            </div>

                            <div v-if="s_job.job.salary_min" class="mb-2">
                                RM {{s_job.job.salary_min}} - RM {{s_job.job.salary_max}}
                            </div>

                            <span class="text-muted mb-2">{{s_job.job.description}}</span>

                            <div class="text-muted mb-2">Posted {{s_job.job_created_at_human}}</div>

                            <div v-if="s_job.job.status != 0 && !s_job.is_applied">
                                <button class="btn btn-primary" @click="apply(s_job)">Apply</button>
                            </div>
                        </div>
                        <div class="card-footer text-muted" v-if="s_job.job.status == 0">
                            This job is no longer active
                        </div>
                    </div>
                </div>
                <div v-else class="d-flex flex-column align-items-center gap-3">
                    <i class="fas fa-briefcase" style="font-size: 3rem;"></i>
                    No saved jobs yet
                </div>
            </div>

            <div v-if="currentTab === 'applied'">
                <div v-if="applied_jobs.length">
                    <div class="mb-2" v-if="countAppliedJobs">{{countAppliedJobs}} jobs</div>

                    <div class="card mb-2" v-for="a_job in applied_jobs" :key="a_job.id">
                        <div class="card-body">
                            <div v-if="a_job.confirmed_slot" class="alert alert-success">
                                Your interview has been confirmed for 
                                {{ $moment(a_job.confirmed_slot).format('dddd, MMMM D, YYYY [at] h:mm A') }}.
                            </div>

                            <div v-if="a_job.interview_status == 1" class="alert alert-info">
                                Your interview slot has been submitted.  
                                Please wait for confirmation from the administrator.
                            </div>

                            <div class="text-capitalize mb-2">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <u><b>{{a_job.job.title}}</b></u>
                                    <span class="badge bg-warning" v-if="a_job.status == 0">Pending</span>
                                    <span class="badge bg-info" v-else-if="a_job.status == 1">Matched</span>
                                    <span class="badge bg-primary" v-else-if="a_job.status == 2">Shortlisted</span>
                                    <span class="badge bg-success" v-else-if="a_job.status == 3">Interview Confirmed</span>
                                    <span class="badge bg-danger" v-else=>Rejected</span>
                                </div>
                            
                                <div v-if="a_job.job.company">{{a_job.job.company.name}}</div>
                            </div>

                            <div v-if="a_job.job.company" class="mb-2">
                                {{a_job.job.company.city}}, {{a_job.job.company.state}}
                            </div>

                            <div v-if="a_job.job.salary_min && a_job.job.salary_max" class="mb-2">
                                RM {{a_job.job.salary_min}} - RM {{a_job.job.salary_max}}
                            </div>
                        
                            <span class="text-muted">{{a_job.job.description}}</span>

                            <div v-if="a_job.interview_slots && a_job.interview_slots.length">
                                <div v-if="a_job.interview_status == 0">
                                    <hr>

                                    <p>Please choose one of the proposed interview slots:</p>

                                    <div class="d-flex gap-2 mb-3">
                                        <button
                                            v-for="(slot, index) in a_job.interview_slots" 
                                            :key="index"
                                            class="btn btn-outline-primary"
                                            @click="selectSlot(a_job.id, slot)"
                                            :disabled="isSelect"
                                        >
                                        {{ $moment(slot).format('YYYY-MM-DD HH:mm A') }}
                                        </button>
                                    </div>

                                    <p>
                                        Don’t see a suitable time? 
                                        <a href="#" @click.prevent="showSuggestions = !showSuggestions">
                                            Suggest alternative slots
                                        </a>
                                    </p>

                                    <div v-if="showSuggestions">
                                        <div class="mb-3" v-for="(slot, index) in form.slots" :key="index">
                                            <VueDatePicker
                                            v-model="form.slots[index]"
                                            :min-date="new Date()"
                                            :placeholder="`Select alternative slot ${index + 1}`"
                                            />
                                        </div>
                                        <div class="text-end">
                                            <button class="btn btn-primary" @click="submitSlot(a_job.id)" :disabled="isSubmit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="d-flex flex-column align-items-center gap-3">
                    <i class="fas fa-briefcase" style="font-size: 3rem;"></i>
                    No applications yet
                </div>
            </div>
        </div>   
    </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
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
        const route = useRoute();
        const saved_jobs = ref([]);
        const applied_jobs = ref([]);
        const loading = ref(false);
        const search_item = ref(null);
        const countSavedJobs = ref(null);
        const countAppliedJobs = ref(null);
        const currentTab = ref(route.query.type || 'saved'); 

        const isSubmit = ref(false);
        const isSelect = ref(false);
        const showSuggestions = ref(false);

        const form = ref({
            slots: [null, null, null]
        });

        const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get(`/api/user/getAppliedJob?tab=${currentTab.value}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('user_access_token')}`,
                    }
                });

                saved_jobs.value = response.data.savedJobs;
                applied_jobs.value = response.data.appliedJobs;
                countSavedJobs.value = response.data.countSavedJobs;
                countAppliedJobs.value = response.data.countAppliedJobs;

            } catch (error) {
                console.error("Error fetching jobs:", error);

                if (error.response?.status) {
                    Swal.fire({
                        title: 'Fetching jobs failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error',
                        allowOutsideClick: false,  
                        allowEscapeKey: false,
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            router.back();
                        }
                    });
                }

            } finally {
                loading.value = false;
            }
        };

        onMounted(getData);

        const apply = async (job) => {
            router.push({ name: 'ApplyJob', params: { id: job.id }});
        };

        const selectSlot = async (application_id, slot) => {
            if (isSelect.value) return;

            isSelect.value = true;

            try {
                const response = await axios.post(`/api/user/application/${application_id}/selectSlot`,
                    { selected_slot: slot },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('user_access_token')}`,
                        }
                    }
                );
                
                Swal.fire({
                    title: 'Slot selected successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                getData();

            } catch (error) {
                console.error("There was an error selecting interview schedule:", error); 

                if (error.response?.status) {
                    Swal.fire({
                        title: 'Selecting interview schedule failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error',
                        allowOutsideClick: false,  
                        allowEscapeKey: false,
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            router.back();
                        }
                    });
                }

            } finally {
                isSelect.value = false;
            }
        };

        const submitSlot = async (application_id) => {
            if (isSubmit.value) return;

            isSubmit.value = true;

            try{
                const response = await axios.post(`/api/user/application/${application_id}/suggestSlot`,
                { suggested_slots: form.value.slots.filter(slot => slot !== null) },
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('user_access_token')}`,
                    },
                }
                );

                Swal.fire({
                    title: 'Slot submitted successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                getData();

            } catch (error) {
                console.error("There was an error submitting interview schedule:", error); 

                if (error.response?.status) {
                    Swal.fire({
                        title: 'Submitting interview schedule failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error',
                        allowOutsideClick: false,  
                        allowEscapeKey: false,
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // router.back();
                        }
                    });
                }

            } finally {
                isSubmit.value = false;
            }
        };

        watch(
            () => route.query.type,
            (newType) => {
                if (newType) {
                    currentTab.value = newType;
                    getData();
                }
            }
        );
      
        return {
           saved_jobs,
           applied_jobs,
           loading,
           countSavedJobs,
           apply,
           currentTab,
           isSubmit,
           isSelect,
           form,
           selectSlot,
           submitSlot,
           showSuggestions,
           countAppliedJobs
        };
    }
};
</script>

<style scoped>
   .custom-tabs {
        border-bottom: none; 
        margin-left: 0;       /* optional: align with parent container */
        padding-left: 0;      /* optional: align with parent container */
    }

    .custom-tabs .nav-item {
        padding: 0; /* removes extra spacing around each tab */
        margin: 0;  /* optional: remove margin if needed */
    }

    .custom-tabs .nav-link {
        position: relative;
        display: inline-block;
        padding: 0.5rem 0; /* vertical spacing only */
        color: #333;
        background: none;
        border: none;
        cursor: pointer;
    }

    .custom-tabs .nav-link.active {
        color: #007bff;
    }

    .custom-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 95px; /* shorter underline */
        height: 2px;
        background-color: #007bff;
    }
</style>
