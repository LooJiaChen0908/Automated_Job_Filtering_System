<template>
    <div>
        <div class="d-flex align-items-center justify-content-between mb-5">
            <h2>Job List</h2>

            <router-link to="/admin/job/create" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add New
            </router-link>
        </div>

        <Loading v-if="loading" />

        <div class="card mb-4" v-else>
            <div class="card-header d-flex align-items-center justify-content-between">
                Advanced Search
                <i class="fas fa-chevron-down" style="cursor: pointer;"></i>
                <!-- can add button to close it -->
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            Title:
                            <input type="text" class="form-control" v-model="form.title"  @keyup.enter="search">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            Work Mode:
                            <v-select :options="['On-site','Remote','Hybrid']" v-model="form.work_mode" placeholder="Select work mode"></v-select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            Salary Range:
                            <div class="d-flex align-items-center">
                                <span class="input-group-text">RM</span>
                                <input type="number" class="form-control" v-model="form.salary_min" min="1">
                                <span class="input-group-text">-</span>
                                <span class="input-group-text">RM</span>
                                <input type="number" class="form-control" v-model="form.salary_max" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            Employment Type:
                            <v-select :options="employment_types" label="name" :reduce="type => type.id" v-model="form.employment_type" placeholder="Select work type"></v-select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <button class="btn btn-danger" @click="resetForm"><i class="fas fa-eraser"></i></button>
                <button class="btn btn-primary" @click="search"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div v-if="!loading && !jobs.length">
            <p v-if="searched">No jobs match your search criteria. Try adjusting the filters.</p>
            <p v-else>No jobs available. Please add some.</p>
        </div>

        <div v-if="jobs.length && !loading">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Job Detail</th>
                        <th scope="col">Work Mode</th>
                        <th scope="col">Employment Type</th>
                        <th scope="col">Salary Range (RM)</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(job, index) in jobs" :key="job.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-capitalize">{{ job.title }}</span>
                                <span class="badge bg-warning" v-if="job.specialization_name">{{ job.specialization_name }}</span>
                            </div>

                            <!-- {{ job.description }} -->

                            <div v-if="job.company" class="text-capitalize">
                                {{ job.company.name }}
                            </div>
                        </td>
                        <td>{{ job.work_mode }}</td>
                        <td>
                            <div v-if="job.employment_type">
                                {{ job.employment_type.charAt(0).toUpperCase() + job.employment_type.slice(1) }}
                            </div>
                            <div v-else>
                                -
                            </div>
                        </td>
                        <td>
                            <div v-if="job.salary_min && job.salary_max">
                                {{ job.salary_min }} - {{ job.salary_max }}
                            </div>
                            <div v-else>
                                -
                            </div>
                        </td>
                        <td>
                            <span class="text-danger" v-if="job.status == 0">Inactive</span>
                            <span class="text-success" v-else>Active</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-success" @click="editJob(job)"><i class="fas fa-edit"></i>&nbsp;Edit</button>
                                <button class="btn btn-danger" @click="deleteJob(job)"><i class="fas fa-trash-alt"></i>&nbsp;Delete</button>
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
    name: 'Job',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const jobs = ref([]);
        const loading = ref(false);
        const form = reactive({
            title: '',
            work_mode: '',
            salary_min: '',
            salary_max: '',
            employment_type: '',
        });

        const searched = ref(false);

        const employment_types = ref([
            { id: 'full-time', name: 'Full-time' },
            { id: 'part-time', name: 'Part-time' },
            { id: 'temporary', name: 'Temporary' },
            { id: 'internship', name: 'Internship' },
        ]);

        // const copied = ref({ index: null, field: null });

       const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/admin/job');
                jobs.value = response.data.data;
            } catch (error) {
                console.error("Error fetching jobs:", error);
            } finally {
                loading.value = false;
            }
        };

        const resetForm = () => {
            form.title = '';
            form.work_mode = '';
            form.salary_min = '';
            form.salary_max = '';
            form.employment_type = '';
            searched.value = false; 
            getData();
        };

        onMounted(getData);

        const search = async () => {
            searched.value = true;

            try {
                const response = await axios.get('/api/admin/job/search', {
                    params: {
                        title: form.title,
                        work_mode: form.work_mode,
                        salary_min: form.salary_min,
                        salary_max: form.salary_max,
                        employment_type: form.employment_type,
                    }
                });

                jobs.value = response.data.jobs;

            } catch (error) {
                console.error("Error fetching job", error);
            }
        };

        const editJob = async (job) => {
            router.push({ name: 'EditJob', params: { id: job.id }});
        };

        const deleteJob = async (job) => {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete ${job.title || ''}!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/admin/job/deleteJob/${job.id}`);
                    Swal.fire({
                        title: 'Deleted successfully',
                        icon: 'success',
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Ok'
                    });
                    await getData();
                } catch (error) {
                    console.error("There was an error deleting the job:", error);
                }
            }
        };

        return {
           jobs,
           loading,
           editJob,
           deleteJob,
           search,
           form,
           employment_types,
           resetForm,
           searched,
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
