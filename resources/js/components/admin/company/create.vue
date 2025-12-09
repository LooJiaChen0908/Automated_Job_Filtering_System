<template>
    <div>
        <h2>Create Cew Company</h2>

        <div class="form-group mb-3">
            <label class="form-label">Name:</label>
            <input type="text" class="form-control" v-model="form.name">
            <span v-if="validationErrors.name" class="text-danger">{{ validationErrors.name[0] }}</span>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Email:</label>
            <input type="text" class="form-control" v-model="form.contact_email">
            <span v-if="validationErrors.contact_email" class="text-danger">{{ validationErrors.contact_email[0] }}</span>
            <!-- contact_email -->
        </div> 

        <div class="form-group mb-3">
            <label class="form-label">Address:</label>
            <input type="text" class="form-control" v-model="form.address">
            <span v-if="validationErrors.address" class="text-danger">{{ validationErrors.address[0] }}</span>
            <!-- text area ? -->
        </div>

        <div class="form-group mb-3">
            <label class="form-label">State:</label>
            <v-select :options="states" v-model="form.state" placeholder="Select state"></v-select>
            <span v-if="validationErrors.state" class="text-danger">{{ validationErrors.state[0] }}</span>
            <!-- <v-select
                :options="states"
                v-model="form.state"
                placeholder="Select state"
                @input="updateCities">
            </v-select>

            <v-select
                :options="availableCities"
                v-model="form.city"
                placeholder="Select city"
                :disabled="!form.state"
            ></v-select> -->
        </div>


        <div class="form-group mb-3">
            <label class="form-label">City:</label>
            <v-select
                id="city"
                v-model="form.city"
                :options="cities"
                placeholder="Select or type your city"
                taggable       
                push-tags       
                searchable
                clearable
            />
            <span v-if="validationErrors.city" class="text-danger">{{ validationErrors.city[0] }}</span>

        </div>
      
        <div class="form-group mb-3">
            <label class="form-label">Country:</label>
            <v-select :options="countries" v-model="form.country" label="name" :reduce="country => country.id" placeholder="Select country"></v-select>
            <span v-if="validationErrors.country" class="text-danger">{{ validationErrors.country[0] }}</span>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Industry:</label>
            <v-select :options="industries" v-model="form.industry" label="label" :reduce="industry => industry.value" placeholder="Select industry"></v-select>
            <!-- can type then filter by word -->
            <span v-if="validationErrors.industry" class="text-danger">{{ validationErrors.industry[0] }}</span>
        </div>

        add image multiple?

        <div class="form-group mb-3">
            <label class="form-label">Profile Image:</label>
            <input 
                type="file" 
                class="form-control" 
                multiple 
                @change="handleFileUpload"
            />
        </div>

        <!-- optional preview -->
        <div v-if="previewUrls.length" class="mt-3">
            <div v-for="(url, index) in previewUrls" :key="index" class="mb-2">
                <img :src="url" alt="Preview" width="120" style="border-radius: 8px;" />
            </div>
        </div>
       
        <div class="form-group text-end">
            <button class="btn btn-primary" @click="submit" :disabled="isSubmit">Submit</button>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
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

        const form = reactive({
            name: '',
            contact_email: '',
            address: '',
            city: '',
            state: '',
            country: '',
            industry: '',
            // images: '',
        });

        const countries = ref([
            { id: 'MY', name: 'Malaysia' },
            { id: 'SG', name: 'Singapore' },
            { id: 'TH', name: 'Thailand' },
            { id: 'ID', name: 'Indonesia' },
            { id: 'PH', name: 'Philippines' },
            { id: 'VN', name: 'Vietnam' },
            { id: 'CN', name: 'China' }
        ]);

        const industries = ref([
            { label: 'Information Technology (IT)', value: 'it' },
            { label: 'Finance & Banking', value: 'finance' },
            { label: 'Education & Training', value: 'education' },
            { label: 'Healthcare & Medical', value: 'healthcare' },
            { label: 'Manufacturing', value: 'manufacturing' },
            { label: 'Retail & Consumer Goods', value: 'retail' },
            { label: 'Food & Beverage', value: 'food_beverage' },
            { label: 'Tourism & Hospitality', value: 'tourism' },
            { label: 'Construction & Real Estate', value: 'construction' },
            { label: 'Transportation & Logistics', value: 'logistics' },
        ]);

        const states = ref([
            "Johor",
            "Kedah",
            "Kelantan",
            "Melaka",
            "Negeri Sembilan",
            "Pahang",
            "Perak",
            "Perlis",
            "Pulau Pinang",
            "Sabah",
            "Sarawak",
            "Selangor",
            "Terengganu",
            "Kuala Lumpur",
            "Putrajaya",
            "Labuan"
        ]);

        const cities = ref([
            'Kuala Lumpur',
            'Shah Alam',
            'Petaling Jaya',
            'Subang Jaya',
            'Johor Bahru',
            'Iskandar Puteri',
            'Melaka',
            'Seremban',
            'Ipoh',
            'George Town',
            'Butterworth',
            'Kuantan',
            'Kota Bharu',
            'Kuala Terengganu',
            'Alor Setar',
            'Kangar',
            'Kota Kinabalu',
            'Sandakan',
            'Tawau',
            'Kuching',
            'Miri',
            'Bintulu',
            'Sibu'
        ]);

        // const cities = {
        // "Johor": ["Johor Bahru", "Batu Pahat", "Muar", "Kluang", "Segamat"],
        // "Kedah": ["Alor Setar", "Sungai Petani", "Kulim", "Langkawi"],
        // "Kelantan": ["Kota Bharu", "Pasir Mas", "Tanah Merah"],
        // "Melaka": ["Melaka City", "Alor Gajah", "Jasin"],
        // "Negeri Sembilan": ["Seremban", "Port Dickson", "Nilai"],
        // "Pahang": ["Kuantan", "Temerloh", "Bentong"],
        // "Perak": ["Ipoh", "Taiping", "Teluk Intan", "Sitiawan"],
        // "Perlis": ["Kangar", "Arau"],
        // "Pulau Pinang": ["George Town", "Butterworth", "Bukit Mertajam"],
        // "Sabah": ["Kota Kinabalu", "Sandakan", "Tawau", "Lahad Datu"],
        // "Sarawak": ["Kuching", "Miri", "Sibu", "Bintulu"],
        // "Selangor": ["Shah Alam", "Petaling Jaya", "Klang", "Kajang", "Subang Jaya"],
        // "Terengganu": ["Kuala Terengganu", "Kemaman", "Dungun"],
        // "Kuala Lumpur": ["Kuala Lumpur"],
        // "Putrajaya": ["Putrajaya"],
        // "Labuan": ["Labuan"]
        // };

        const availableCities = ref([]);

        const updateCities = () => {
            // availableCities.value = cities[form.value.state] || [];
            // form.value.city = null;
        };

        // Handle file upload
        const handleFileUpload = (event) => {
            files.value = Array.from(event.target.files)
            previewUrls.value = files.value.map(file => URL.createObjectURL(file))
        }

        const submit = async () => {
            if (isSubmit.value) return;

            isSubmit.value = true;

            const formData = new FormData()
            // append text fields
            for (const key in form) {
                formData.append(key, form[key])
            }

            // append image files
            files.value.forEach((file, index) => {
                formData.append(`images[${index}]`, file)
            })

            // console.log('length:' + files.value.length)

            try {
                const response = await axios.post('/api/admin/company/create', formData, {
                    headers: { 'Content-Type': 'mutipart/form-data' }
                });

                Swal.fire({
                    title: 'Added successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                router.push('/admin/company');

            } catch (error) {
                console.error('Create failed:', error.response?.data || error.message);
                
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

        onMounted(() => {
           
        });

        return {
            form,
            submit,
            validationErrors,
            isSubmit,
            countries,
            industries,
            states,
            availableCities,
            updateCities,
            cities,
            handleFileUpload,
            previewUrls
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
