<template>
    <div>
        <h2>Edit Job</h2>

        <Loading v-if="loading" />

        <div v-else>
            <div class="form-group mb-3">
                <label class="form-label">Job Title:</label>
                <input type="text" class="form-control title-input" v-model="form.title">
                <span v-if="validationErrors.title" class="text-danger">{{ validationErrors.title[0] }}</span>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Company:</label>
                <v-select :options="companies" v-model="form.company_id" label="name" :reduce="company => company.id" placeholder="Select a company"></v-select>
                <span v-if="validationErrors.company_id" class="text-danger">{{ validationErrors.company_id[0] }}</span>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Description:</label>
                <input type="text" class="form-control" v-model="form.description">
                <span v-if="validationErrors.description" class="text-danger">{{ validationErrors.description[0] }}</span>
            </div> 

            <div class="form-group mb-3">
                <label class="form-label"></label>Work Mode:
                <v-select :options="['On-site','Remote','Hybrid']" v-model="form.work_mode" placeholder="Select work mode"></v-select>
                <span v-if="validationErrors.work_mode" class="text-danger">{{ validationErrors.work_mode[0] }}</span>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Work location:</label>
                <input type="text" class="form-control" v-model="form.work_location">
                <span v-if="validationErrors.work_location" class="text-danger">{{ validationErrors.work_location[0] }}</span>
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
                    label="name"
                    :reduce="option => option.id"
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

            <div class="form-group mb-3">
                <label class="form-label">Status:</label>
                <v-select
                    :options="[{ label: 'Active', value: 1 }, { label: 'Inactive', value: 0 }]"
                    v-model="form.status"
                    label="label"
                    :reduce="option => option.value"
                    placeholder="Select job status"
                />
            </div>

            <div class="form-group text-end">
                <button class="btn btn-primary" @click="submit" :disabled="isSubmit">Submit</button>
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
            title: '',
            company_id: '',
            description: '',
            work_mode: '',
            work_location: '',
            salary_min: '',
            salary_max: '',
            specialization: '',
            required_experience_years: '',
            employment_type: '',
            status: '',
            education_level: '',
        });
        const validationErrors = reactive({});
        const isSubmit = ref(false);
        const router = useRouter();
        const companies = ref([]);

        const employment_types = ref([
            { id: 'full-time', name: 'Full-time' },
            { id: 'part-time', name: 'Part-time' },
            { id: 'temporary', name: 'Temporary' },
            { id: 'internship', name: 'Internship' },
        ]);

        const specializations = [
            { id: 'software-development', name: 'Software Development' },
            { id: 'web-development', name: 'Web Development' },
            { id: 'mobile-app-development', name: 'Mobile App Development' },
            { id: 'data-science', name: 'Data Science' },
            { id: 'cybersecurity', name: 'Cybersecurity' },
            { id: 'cloud-computing', name: 'Cloud Computing' },
            { id: 'ui-ux-design', name: 'UI/UX Design' },
            { id: 'network-engineering', name: 'Network Engineering' },
            { id: 'qa-testing', name: 'QA & Testing' },
            { id: 'accounting', name: 'Accounting' },
            { id: 'finance', name: 'Finance' },
            { id: 'business-analysis', name: 'Business Analysis' },
            { id: 'marketing', name: 'Marketing' },
            { id: 'sales', name: 'Sales' },
            { id: 'human-resources', name: 'Human Resources' },
            { id: 'graphic-design', name: 'Graphic Design' },
            { id: 'content-writing', name: 'Content Writing' },
            { id: 'social-media', name: 'Social Media Marketing' },
            { id: 'video-production', name: 'Video Production' },
            { id: 'animation', name: 'Animation' },
            { id: 'nursing', name: 'Nursing' },
            { id: 'pharmacy', name: 'Pharmacy' },
            { id: 'biotechnology', name: 'Biotechnology' },
            { id: 'public-health', name: 'Public Health' },
            { id: 'education', name: 'Education' },
            { id: 'logistics', name: 'Logistics & Supply Chain' },
            { id: 'legal', name: 'Legal Services' },
            { id: 'hospitality', name: 'Hospitality & Tourism' },
            { id: 'environmental-science', name: 'Environmental Science' },
            { id: 'translation', name: 'Translation & Interpretation' }
        ];

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

        const fetchJob = async () => {
            loading.value = true;
            try {
                const { data } = await axios.get(`/api/admin/job/getJob/${props.id}`);

                Object.keys(form).forEach(key => {
                    if (data.job[key] !== undefined && data.job[key] !== null) {
                        form[key] = data.job[key];
                    }
                });

                companies.value = data.companies;

            } catch (error) {
                console.error("There was an error fetching job:", error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(fetchJob);

        const submit = async () => {
            if (isSubmit.value) return;
            isSubmit.value = true;

            try {
                await axios.put(`/api/admin/job/updateJob/${props.id}`, form);

                Swal.fire({
                    title: 'Updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                router.push('/admin/job');

            } catch (error) {
                if (error.response?.status === 422) {
                    // Clear old errors
                    Object.keys(validationErrors).forEach(key => delete validationErrors[key]);
                    // Assign new errors
                    Object.assign(validationErrors, error.response.data.errors);
                } else {
                    Swal.fire({
                        title: 'Failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error'
                    });
                }

            } finally {
                isSubmit.value = false;
            }
        };

        return {
            loading,
            form,
            validationErrors,
            isSubmit,
            submit,
            employment_types,
            companies,
            specializations,
            experienceOptions,
            educationLevels,
        };
    }
};
</script>

<style scoped>
    .title-input{
        text-transform: capitalize;
    }
</style>
