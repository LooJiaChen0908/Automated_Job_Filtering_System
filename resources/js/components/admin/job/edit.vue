<template>
    <div>
        <h2>Edit Job</h2>

        <Loading v-if="loading" />

        <div v-else>
            <div class="form-group mb-3">
                Job Title:
                <input type="text" class="form-control" v-model="form.title">
            </div>

            <div class="form-group mb-3">
                Company:
                <v-select :options="companies" v-model="form.company_id" label="name" :reduce="company => company.id" placeholder="Select a company"></v-select>
            </div>

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
                <!-- <input type="text" class="form-control" v-model="form.specialization"> -->
                <v-select
                    :options="specializations"
                    v-model="form.specialization"
                    label="name"
                    :reduce="option => option.id"
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

            <div class="form-group mb-3">
                Status:
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
            salary_min: '',
            salary_max: '',
            specialization: '',
            required_experience_years: '',
            employment_type: '',
            status: '',
            education_level: '',
        });
        const errors = reactive({});
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
                if (error.response && error.response.status === 422) {
                    Object.assign(errors, error.response.data.errors);
                } else {
                    console.error('Error updating job:', error);
                }
            } finally {
                isSubmit.value = false;
            }
        };

        return {
            loading,
            form,
            errors,
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
/* Add styles as needed */
</style>
