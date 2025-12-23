<template>
    <div class="p-4" style="background-color: #bfbbbb;">
        <h2>Update Profile</h2>
        
        <Loading v-if="loading" />

        <div class="row mt-2" v-else>
            <div class="col-6">
                <div class="form-group mb-2">
                    <label>Full name</label>
                    <input type="text" class="form-control name-input" placeholder="Full Name" v-model="form.name">
                    <span v-if="validationErrors.name" class="text-danger">{{ validationErrors.name[0] }}</span>
                </div>

                <div class="form-group mb-2">
                    <label>Gender</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="radio" v-model="form.gender" id="male" value="male" name="gender"><label for="male">Male</label>
                        <input type="radio" v-model="form.gender" id="female" value="female" name="gender"><label for="female">Female</label>
                    </div>
                    <span v-if="validationErrors.gender" class="text-danger">{{ validationErrors.gender[0] }}</span>
                </div>

                <div class="form-group mb-2">
                    <label>Contact Number</label>
                    <div class="input-group">
                        <span class="input-group-text" id="salary">+60</span>
                        <input type="text" class="form-control" v-model="form.contact_no">
                    </div>
                    <span v-if="validationErrors.contact_no" class="text-danger">{{ validationErrors.contact_no[0] }}</span>
                </div>

                <div class="form-group mb-2">
                    <label>Salary Expectation</label>
                    <div class="input-group">
                        <span class="input-group-text" id="expected_salary">RM</span>
                        <input type="number" class="form-control" v-model="form.expected_salary"  aria-describedby="expected_salary" min="1">
                        <span v-if="validationErrors.expected_salary" class="text-danger">{{ validationErrors.expected_salary[0] }}</span>
                    </div>
                </div>

                <div class="form-group mb-2">
                    <label>Country</label>
                    <v-select :options="countries" v-model="form.country" label="name" :reduce="country => country.id" placeholder="Select country"></v-select>
                    <span v-if="validationErrors.country" class="text-danger">{{ validationErrors.country[0] }}</span>
                </div>

                <div class="form-group mb-2">
                    <label>Religion</label>
                    <v-select :options="religions" v-model="form.religion" label="name" :reduce="religion => religion.id"placeholder="Select Religion"></v-select>
                    <span v-if="validationErrors.religion" class="text-danger">{{ validationErrors.religion[0] }}</span>
                </div>
            </div>

            <div class="col-6">
                <div class="form-group mb-2">
                    <label>Birth Date</label>
                    <div class="d-flex align-items-center gap-2">
                        <v-select class="w-33" :options="day" placeholder="Day" v-model="form.day"></v-select> /
                        <v-select class="w-33":options="month" placeholder="Month" v-model="form.month" label="name" :reduce="month => month.id"></v-select> /
                        <v-select class="w-33" :options="year" placeholder="Year" v-model="form.year"></v-select>
                    </div>
                    <span v-if="validationErrors.day" class="text-danger">{{ validationErrors.day[0] }}</span>
                    <span v-if="validationErrors.month" class="text-danger">{{ validationErrors.month[0] }}</span>
                    <span v-if="validationErrors.year" class="text-danger">{{ validationErrors.year[0] }}</span>
                </div>
                
                <div class="form-group mb-2">
                    <label>Preferred Work types</label>
                    <v-select :options="employment_types" v-model="form.preferred_work_types" label="name" :reduce="type => type.id" placeholder="Select employment type" multiple></v-select>
                    <span v-if="validationErrors.preferred_work_types" class="text-danger">{{ validationErrors.preferred_work_types[0] }}</span>
                </div>

                <div class="form-group mb-2">
                    <label>Highest education level</label>
                    <v-select
                        :options="educationLevels"
                        v-model="form.highest_qualification"
                        label="name"
                        :reduce="option => option.id"
                        placeholder="Select education level"
                    />
                    <span v-if="validationErrors.highest_qualification" class="text-danger">{{ validationErrors.highest_qualification[0] }}</span>
                </div>

                <div class="form-group mb-2">
                    <label>Work Experiences</label>
                    <v-select
                        :options="experienceOptions"
                        v-model="form.work_experience"
                        label="name"
                        :reduce="option => option.id"
                        placeholder="Select work experience"
                    />
                    <span v-if="validationErrors.work_experience" class="text-danger">{{ validationErrors.work_experience[0] }}</span>
                </div>

                <div class="form-group">
                    <label>Specialization</label>
                    <v-select :options="specializations" v-model="form.specialization" label="label" :reduce="option => option.value" placeholder="Select Specialization"></v-select>
                    <span v-if="validationErrors.specialization" class="text-danger">{{ validationErrors.specialization[0] }}</span>
                </div>

                <!-- resume -->
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button class="btn btn-primary" @click="submit" :disabled="isSubmit">Submit</button>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import Loading from '@/components/loading.vue';

export default {
    components: { 
        Loading,
    },
    setup() {
        const validationErrors = reactive({});
        const router = useRouter();
        const isSubmit = ref(false);
        const files = ref([])         // Store the actual file objects
        const userData = ref(null);
        const userProfile = ref([]);
        const specializations = ref([]);
        const loading = ref(false);

        const form = reactive({
            name: '',
            gender: '',
            contact_no: '',
            year: '',
            month: '',
            day: '',
            country: '',
            religion: '',
            work_experience: '',
            expected_salary: '',
            specialization: '',
            employment_type: '',
            highest_qualification: '',
            preferred_work_types: [],
        });

        const day = ref([]);
        for(let i = 1; i <= 31; i++){
            day.value.push(i);
        }

        const month = ref([
            { id: 1, name: 'January' },
            { id: 2, name: 'February' },
            { id: 3, name: 'March' },
            { id: 4, name: 'April' },
            { id: 5, name: 'May' },
            { id: 6, name: 'June' },
            { id: 7, name: 'July' },
            { id: 8, name: 'August' },
            { id: 9, name: 'September' },
            { id: 10, name: 'October' },
            { id: 11, name: 'November' },
            { id: 12, name: 'December' },
        ]);

        const currentYear = new Date().getFullYear();
        const year = ref([]);
        for (let y = currentYear; y >= 1960; y--) {
            year.value.push(y);
        }

        const experienceOptions = [
            { id: 0, name: 'No experience' },
            { id: 1, name: '1 year' },
            { id: 2, name: '2 years' },
            { id: 3, name: '3 years' },
            { id: 5, name: '5+ years' },
            { id: 10, name: '10+ years' },
        ];

        const employment_types = ref([
            { id: 'full-time', name: 'Full-time' },
            { id: 'part-time', name: 'Part-time' },
            { id: 'temporary', name: 'Temporary' },
            { id: 'internship', name: 'Internship' },
        ]);

        const educationLevels = [
            { id: 'none', name: 'None' },
            { id: 'spm', name: 'SPM / O-Level' },
            { id: 'diploma', name: 'Diploma / Advanced Diploma' },
            { id: 'bachelor', name: "Bachelor's Degree" },
            { id: 'master', name: "Master's Degree" },
            { id: 'phd', name: 'Doctorate (PhD)' }
        ];

        const religions = [
            { id: 'islam', name: 'Islam' },
            { id: 'buddhism', name: 'Buddhism' },
            { id: 'christianity', name: 'Christianity' },
            { id: 'hinduism', name: 'Hinduism' },
            { id: 'sikhism', name: 'Sikhism' },
            { id: 'taoism', name: 'Taoism' },
            { id: 'atheism', name: 'Atheism / Free thinker' },
            { id: 'other', name: 'Other' },
        ];

        const countries = ref([
            { id: 'MY', name: 'Malaysia' },
            { id: 'SG', name: 'Singapore' },
            { id: 'TH', name: 'Thailand' },
            { id: 'ID', name: 'Indonesia' },
            { id: 'PH', name: 'Philippines' },
            { id: 'VN', name: 'Vietnam' },
            { id: 'CN', name: 'China' }
        ]);
      
        const submit = async () => {
            if (isSubmit.value) return;

            isSubmit.value = true;

            try {
                const response = await axios.post('/api/user/updateProfile', form, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                    }
                });

                // Optionally fetch the updated profile again
                const profileRes = await axios.get('/api/user/getProfile', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                    }
                });

                const updatedProfile = profileRes.data.applicant;

                // Update localStorage
                const stored = JSON.parse(localStorage.getItem('user_data'));
                const refreshed = {
                    ...stored,
                    ...updatedProfile, // merge updated applicant fields
                };
                localStorage.setItem('user_data', JSON.stringify(refreshed));
                
                Swal.fire({
                    title: 'Profile updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                getProfile();
              
            } catch (error) {
                console.error('Update failed:', error.response?.data || error.message);
            
                if (error.response?.status === 422) {
                    // Clear old errors
                    Object.keys(validationErrors).forEach(key => delete validationErrors[key]);
                    // Assign new errors
                    Object.assign(validationErrors, error.response.data.errors);
                } else {
                    Swal.fire({
                        title: 'Update Failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error'
                    });
                }

            } finally {
                isSubmit.value = false;
            }
        };

        const getProfile = async () => {
            loading.value = true;
            try {
                const { data } = await axios.get('/api/user/getProfile', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                    }
                });

                const applicant = data.applicant;

                Object.keys(form).forEach(key => {
                    if (applicant[key] !== undefined && applicant[key] !== null) {
                        if (key === 'contact_no') {
                            form.contact_no = applicant.contact_no.replace(/^60/, '');
                        } else {
                            form[key] = applicant[key];
                        }
                    }
                });

                if (applicant.birth_date && applicant.birth_date.includes('-')) {
                    const [yearStr, monthStr, dayStr] = applicant.birth_date.split('-');
                    form.year = parseInt(yearStr);
                    form.month = parseInt(monthStr);
                    form.day = parseInt(dayStr);
                }

                userProfile.value = applicant;

            } catch (err) {
                error.value = err.response?.data?.message || 'Error fetching user profile';
            } finally {
                loading.value = false;
            }
        };

        const getSpecializations = async () => {
            try {
                const response = await axios.get('/api/user/getSpecialization');

                specializations.value = response.data.data;

            } catch (err) {
                error.value = err.response?.data?.message || 'Error fetching specialization';
            }
        }

        onMounted(() => {
            getProfile();
            getSpecializations();
            const stored = localStorage.getItem('user_data');
            if(stored){
                userData.value = JSON.parse(stored);
                console.log(userData)
                if(userData.name){
                    form.name = userData.value.name;
                }
            }
        });

        return {
            form,
            submit,
            validationErrors,
            isSubmit,
            day,
            month,
            year,
            countries,
            religions,
            getProfile,
            userData,
            userProfile,
            specializations,
            experienceOptions,
            employment_types,
            educationLevels,
            loading,
        };
    }
};
</script>

<style scoped>
    .name-input{
        text-transform: capitalize;
    }

    .w-33{
        width: 33%;
    }
</style>

