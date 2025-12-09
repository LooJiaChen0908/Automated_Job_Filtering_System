<template>
    <div>
        <h2>Create New Job</h2>

        <div class="form-group mb-3">
            <label class="form-label">Job Title:</label>
            <input type="text" class="form-control" v-model="form.title">
            <span v-if="validationErrors.title" class="text-danger">{{ validationErrors.title[0] }}</span>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Company:</label>
            <v-select :options="companies" v-model="form.company_id" label="name" :reduce="company => company.id" placeholder="Select a company"></v-select>
            <span v-if="validationErrors.company_id" class="text-danger">{{ validationErrors.company_id[0] }}</span>
        </div>

        <!-- if has company then other auto fill -->

        <div class="form-group mb-3">
            <label class="form-label">Description:</label>
            <input type="text" class="form-control" v-model="form.description">
            <span v-if="validationErrors.description" class="text-danger">{{ validationErrors.description[0] }}</span>
        </div> 

        <div class="form-group mb-3">
            <label class="form-label">Work Mode:</label>
            <v-select :options="['On-site','Remote','Hybrid']" v-model="form.work_mode" placeholder="Select work mode"></v-select>
            <span v-if="validationErrors.work_mode" class="text-danger">{{ validationErrors.work_mode[0] }}</span>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Work location:</label>
            <input type="text" class="form-control" v-model="form.work_location">
            <span v-if="validationErrors.work_location" class="text-danger">{{ validationErrors.work_location[0] }}</span>
            <!-- KL BRANCH HQ -->
            <!-- branch sao department ??? -->
        </div> 

        <div class="form-group mb-3">
            <label class="form-label">Salary Range</label>
            <div class="d-flex align-items-center">
                <span class="input-group-text">RM</span>
                <input type="number" class="form-control" v-model="form.salary_min" min="1">
                <span class="input-group-text">-</span>
                <span class="input-group-text">RM</span>
                <input type="number" class="form-control" v-model="form.salary_max" min="1">
            </div>
            <span v-if="validationErrors.salary_min" class="text-danger">{{ validationErrors.salary_min[0] }}</span>
            <span v-if="validationErrors.salary_max" class="text-danger">{{ validationErrors.salary_max[0] }}</span>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Employment type:</label>
            <v-select :options="employment_types" v-model="form.employment_type" label="name" :reduce="type => type.id" placeholder="Select employment type"></v-select>
            <!-- contact_email -->
            <span v-if="validationErrors.employment_type" class="text-danger">{{ validationErrors.employment_type[0] }}</span>
        </div> 
        
         <div class="form-group mb-3">
            <label class="form-label">Specialization:</label>
            <v-select
                :options="specializations"
                v-model="form.specialization"
                label="label"
                :reduce="option => option.value"
                placeholder="Select specialization"
            />
            <span v-if="validationErrors.specialization" class="text-danger">{{ validationErrors.specialization[0] }}</span>
        </div> 

        <div class="form-group mb-3">
            <label class="form-label">Required experience years:</label>
            <v-select
                :options="experienceOptions"
                v-model="form.required_experience_years"
                label="name"
                :reduce="option => option.id"
                placeholder="Select required experience years"
            />
            <span v-if="validationErrors.required_experience_years" class="text-danger">{{ validationErrors.required_experience_years[0] }}</span>
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Required Education level</label>
            <v-select
                :options="educationLevels"
                v-model="form.education_level"
                label="name"
                :reduce="option => option.id"
                placeholder="Select education level"
            />
            <span v-if="validationErrors.education_level" class="text-danger">{{ validationErrors.education_level[0] }}</span>
        </div>

        <div class="form-group text-end">
            <button class="btn btn-primary" @click="submit" :disabled="isSubmit">Submit</button>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    setup() {
        const validationErrors = reactive({});
        const router = useRouter();
        const isSubmit = ref(false);
        const files = ref([])         // Store the actual file objects
        const previewUrls = ref([])   // Store preview image URLs
        const companies = ref([]);
        const specializations = ref([]);

        const form = reactive({
            company_id: '',
            title: '',
            description: '',
            work_location: '',
            work_mode: '',
            employment_type: '',
            required_experience_years: '',
            specialization: '',
            salary_min: '',
            salary_max: '',
            education_level: '',
        });

        const employment_types = ref([
            { id: 'full-time', name: 'Full-time' },
            { id: 'part-time', name: 'Part-time' },
            { id: 'temporary', name: 'Temporary' },
            { id: 'internship', name: 'Internship' },
        ]);

        const experienceOptions = [
            { id: 0, name: 'No experience' },
            { id: 1, name: '1 year' },
            { id: 2, name: '2 years' },
            { id: 3, name: '3 years' },
            { id: 5, name: '5+ years' },
            { id: 10, name: '10+ years' },
        ];

        const educationLevels = [
            { id: 'none', name: 'No formal education required' },
            { id: 'spm', name: 'SPM / O-Level' },
            { id: 'diploma', name: 'Diploma / Advanced Diploma' },
            { id: 'bachelor', name: "Bachelor's Degree" },
            { id: 'master', name: "Master's Degree" },
            { id: 'phd', name: 'Doctorate (PhD)' }
        ];

        const getCompany = async () => {
            try {
                const res = await axios.get('/api/admin/job/getCompanyName');
                companies.value = res.data.companies;
                specializations.value = res.data.specializations;
            } catch (error) {
                console.error('Error fetching companies:', error);
            }
        };

        const submit = async () => {
            if (isSubmit.value) return;

            isSubmit.value = true;

            try {
                const response = await axios.post('/api/admin/job/create', form);

                Swal.fire({
                    title: 'Added successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                router.push('/admin/job');

            } catch (error) {
                console.error('Create job failed:', error.response?.data || error.message);

                if (error.response?.status === 422) {
                    // Clear old errors
                    Object.keys(validationErrors).forEach(key => delete validationErrors[key]);
                    // Assign new errors
                    Object.assign(validationErrors, error.response.data.errors);
                } else {
                    Swal.fire({
                        title: 'Create job failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error'
                    });
                }

            } finally {
                isSubmit.value = false;
            }
        };

        watch(
            () => form.company_id,
            async (newVal, oldVal) => {
                if (newVal) {
                    console.log(newVal)

                    const selectedCompany = companies.value.find(c => c.id === newVal);
                    if (selectedCompany) {
                        console.log('here '+ selectedCompany.id)
                        form.specialization = selectedCompany.industry || '';
                        // form.work_location = selectedCompany.address || '';
                        // console.log()
                    }
                }
            }
        )

        onMounted(() => {
           getCompany();
        });

        return {
            form,
            submit,
            validationErrors,
            isSubmit,
            companies,
            employment_types,
            specializations,
            experienceOptions,
            educationLevels,
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
