<template>
    <div>
        <h2>Create New Job</h2>

        <div class="form-group mb-3">
            Job Title:
            <input type="text" class="form-control" v-model="form.title">
        </div>

        <div class="form-group mb-3">
            Company:
            <v-select :options="companies" v-model="form.company_id" label="name" :reduce="company => company.id" placeholder="Select a company"></v-select>
        </div>

        <!-- if has company then other auto fill -->

        <div class="form-group mb-3">
            Description:
            <input type="text" class="form-control" v-model="form.description">
        </div> 

        <div class="form-group mb-3">
            Work Mode:
            <v-select :options="['On-site','Remote','Hybrid']" v-model="form.work_mode" placeholder="Select work mode"></v-select>
        </div>

        <div class="form-group mb-3">
            Work location:
            <input type="text" class="form-control" v-model="form.work_location">
            <!-- KL BRANCH HQ -->
            <!-- branch sao department ??? -->
        </div> 

        <div class="form-group mb-3">
            Salary Range
            <div class="d-flex align-items-center">
                <span class="input-group-text">RM</span>
                <input type="number" class="form-control" v-model="form.salary_min" min="1">
                <span class="input-group-text">-</span>
                <span class="input-group-text">RM</span>
                <input type="number" class="form-control" v-model="form.salary_max" min="1">
            </div>
        </div>

        <div class="form-group mb-3">
            Employment type:
            <v-select :options="employment_types" v-model="form.employment_type" label="name" :reduce="type => type.id" placeholder="Select employment type"></v-select>
            <!-- contact_email -->
        </div> 
        
         <div class="form-group mb-3">
            Specialization:
            <v-select
                :options="specializations"
                v-model="form.specialization"
                label="label"
                :reduce="option => option.value"
                placeholder="Select specialization"
            />
        </div> 

        <div class="form-group mb-3">
            Required experience years:
            <input type="number" class="form-control" v-model="form.required_experience_years">
            <v-select
                :options="experienceOptions"
                v-model="form.required_experience_years"
                label="name"
                :reduce="option => option.id"
                placeholder="Select required experience years"
            />
        </div>
        
        <div class="form-group mb-3">
            Education level
            <v-select
                :options="educationLevels"
                v-model="form.education_level"
                label="name"
                :reduce="option => option.id"
                placeholder="Select education level"
            />
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
            // images: '',
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
            if (is_submit.value) return;

            is_submit.value = true;

            try {
                const response = await axios.post('/api/admin/job/create', form);

                router.push('/admin/job');

            } catch (error) {
                console.error('Create job failed:', error.response?.data || error.message);
                alert('failed!');
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
