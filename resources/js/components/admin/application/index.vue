<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Application List</h2>
        </div>

        <Loading v-if="loading" />

        <div v-if="!loading && !applications.length">
            <p>No applications available. Please add some.</p>
        </div>

        <div v-if="applications.length">
            <div class="mb-2">
                Filter by:
                <input class="form-control" style="width: 50%;" placeholder="Enter salary">
                <input class="form-control" style="width: 50%;" placeholder="Enter work location">
            </div>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Job Detail</th>
                        <th scope="col">Applicant Info</th>
                        <th scope="col">Resume</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(application, index) in applications" :key="application.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>
                            <div>
                                <span class="text-capitalize">{{ application.job.title }}</span> -  {{ application.job.company.state }}
                            </div>
                            {{ application.job.description }}

                            <p>JOB ID: {{application.job_id}}</p>

                            <!-- {{application}} -->

                            how to schedule ? filter ?

                            <!-- {{application.job}} -->
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                {{ application.applicant.email }} <button class="btn btn-secondary btn-sm" @click="sendEmail(application.applicant.email)"><i class="fas fa-envelope"></i></button>
                            </div>

                            <div class="d-flex align-items-center gap-1">
                                {{ application.applicant.contact_no }} <button class="btn btn-info btn-sm ms-1" @click="call(application.applicant.contact_no)"><i class="fas fa-phone"></i></button>
                            </div>

                            {{application.applicant}}
                        </td>
                        <td>
                            <!-- {{ application.resume_path }} -->
                           <template v-if="application.resume_path.endsWith('.pdf')">
                                <a :href="`/storage/${application.resume_path}`" target="_blank" class="btn btn-info btn-sm ms-1">
                                    View
                                </a>
                            </template>
                            <!-- <template v-else>
                            <a :href="`/storage/${application.resume_path}`" download class="btn btn-warning btn-sm ms-1">
                                <i class="fas fa-download"></i> Download
                            </a>
                            </template> -->

                            <a 
                                v-if="application.resume_path" 
                                :href="`/storage/${application.resume_path}`" 
                                download 
                                class="btn btn-warning btn-sm ms-1"
                                target="_blank"
                            >
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                        <td>
                            <span class="text-warning" v-if="application.status == 0">Pending</span>
                            <span class="text-success" v-else-if="application.status == 1">Approved</span>
                            <span class="text-danger" v-else>Rejected</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-success" @click="editApplication(application)"><i class="fas fa-edit"></i>&nbsp;Edit / Approve</button>
                                <button class="btn btn-danger" @click="deleteApplication(application)"><i class="fas fa-trash-alt"></i>&nbsp;Delete</button>
                                <button class="btn btn-info">Notify User</button>
                                <button class="btn btn-warning" @click="schedule">Schedule interview</button>
                                <!-- application detail -->
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
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
    name: 'Application',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const applications = ref([]);
        const loading = ref(false);
        const copied = ref({ index: null, field: null });

        const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/admin/application');
                applications.value = response.data.data;
            } catch (error) {
                console.error("There was an error fetching applications:", error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(getData);

        const deleteApplication = async () => {
            
        };

        const editApplication = async () => {
            
        };   

        const call = async (phoneNo) => {
            window.location.href = `tel:${phoneNo}`;
        };
        
        const sendEmail = async (email) => {
            if (!email) {
                Swal.fire('No email address found', '', 'warning');
                return;
            }

            // Create a mailto link
            const subject = encodeURIComponent("Job Application Follow-up");
            const body = encodeURIComponent("Hello,\n\nI would like to discuss your application.\n\nRegards,");
            window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
        };

        const download = async () => {

        };

        const schedule = async () => {
            // open modal then can schedule
        };

        return {
           applications,
           loading,
           editApplication,
           deleteApplication,
           call,
           sendEmail,
           download,
           schedule,
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
