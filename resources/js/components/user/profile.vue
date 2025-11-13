<template>
    <div class="p-4" style="background-color: #bfbbbb;">
        <h2>Update Profile</h2>

        <div class="row mt-2">
            <div class="col-6">
                <div class="form-group mb-2">
                    Full name
                    <input type="text" class="form-control" placeholder="Full Name" v-model="form.name">
                </div>

                {{userProfile}}

                <div class="form-group mb-2">
                    <label>Gender</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="radio" v-model="form.gender" id="male" value="male" name="gender"><label for="male">Male</label>
                        <input type="radio" v-model="form.gender" id="female" value="female" name="gender"><label for="female">Female</label>
                    </div>
                </div>

                <div class="form-group mb-2">
                    Contact Number
                    <div class="input-group">
                        <span class="input-group-text" id="salary">+60</span>
                        <input type="text" class="form-control" v-model="form.contact_no">
                    </div>
                </div>

                <div class="form-group mb-2">
                    Salary Expectation
                    <div class="input-group">
                        <span class="input-group-text" id="expected_salary">RM</span>
                        <input type="number" class="form-control" v-model="form.expected_salary"  aria-describedby="expected_salary" min="1">
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="form-group mb-2">
                    Birth Date
                    <div class="d-flex align-items-center gap-2">
                        <v-select :options="day" placeholder="Day" v-model="form.day" style="width: 30%;"></v-select> /
                        <v-select :options="month" placeholder="Month" v-model="form.month" label="name" :reduce="month => month.id" style="width: 30%;"></v-select> /
                        <v-select :options="year" placeholder="Year" v-model="form.year" style="width: 30%;"></v-select>
                    </div>
                </div>
                  
                <div class="form-group mb-2">
                    Country
                    <v-select :options="countries" v-model="form.country" label="name" :reduce="country => country.id" placeholder="Select country"></v-select>
                </div>

                <div class="form-group mb-2">
                    Religion
                    <v-select :options="religions" v-model="form.religion" label="name" :reduce="religion => religion.id"placeholder="Select Religion"></v-select>
                </div>

                <div class="form-group">
                    Working Experiences
                    <div class="d-flex align-items-center">
                        <v-select :options="['No work experience','Month','Year']" placeholder="Select" v-model="form.working_experience" style="width: 30%"></v-select>
                        <input type="number" class="form-control" placeholder="Years" v-if="form.working_experience == 'Year'">
                        <input type="number" class="form-control" placeholder="Years" v-else-if="form.working_experience == 'Month'">
                    </div>
                </div>

                <div class="form-group">
                    Specialization
                    <v-select :options="specializations" v-model="form.specialization" label="label" :reduce="option => option.value" placeholder="Select Specialization"></v-select>
                </div>

                

                highest qualification

                resume
            </div>
        </div>

        {{form}}

        <div class="d-flex justify-content-end">
            <button class="btn btn-primary" @click="submit">Submit</button>
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
        const userData = ref(null);
        const userProfile = ref([]);
        const specializations = ref([]);

        const form = reactive({
            name: '',
            gender: '',
            contact_no: '',
            year: '',
            month: '',
            day: '',
            country: '',
            religion: '',
            working_experience: '',
            expected_salary: '',
            specialization: '',
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
            try {
                const response = await axios.post('/api/user/updateProfile', form);
                console.log('Update success:', response.data);

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
                // localStorage.setItem('user_data', JSON.stringify(updatedProfile));
                
                Swal.fire({
                    title: 'Profile updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                // then go refresh the local storage

                // router.push({
                //     name: 'Login',
                // });
              
            } catch (error) {
                console.error('Update failed:', error.response?.data || error.message);
                alert('Update failed!');
            }
        };

        const getProfile = async () => {
            try {
                await axios.get('/api/user/getProfile', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
                    }
                });

                userProfile.value = response.data.applicant;

            } catch (err) {
                error.value = err.response?.data?.message || 'Error fetching user profile';
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
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>

