<template>
    <div>
        <h2>Edit Company</h2>

        <Loading v-if="loading" />

        <div v-else>
             <div class="form-group mb-3">
                <label class="mb-3">Company Name</label>
                <input type="text" class="form-control" v-model="form.name" placeholder="Enter company name"/>
                <span v-if="errors.name" class="text-danger">{{ errors.name[0] }}</span>
            </div>

            <div class="form-group mb-3">
                <label class="mb-3">Contact Email</label>
                <input type="text" class="form-control" v-model="form.contact_email" placeholder="Enter contact email"/>
                <span v-if="errors.contact_email" class="text-danger">{{ errors.contact_email[0] }}</span>
            </div>

            <div class="form-group mb-3">
                State:
                <!-- <input type="text" class="form-control" v-model="form.state"> -->
                <v-select :options="states" v-model="form.state" placeholder="Select state"></v-select>
            </div>

            <div class="form-group mb-3">
                City:
                <!-- <input type="text" class="form-control" v-model="form.city"> -->
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

            </div>

            <div class="form-group mb-3">
                Country:
                <v-select :options="countries" v-model="form.country" label="name" :reduce="country => country.id" placeholder="Select country"></v-select>
            </div>

            <div class="form-group mb-3">
                Industry:
            <v-select :options="industries" v-model="form.industry" label="label" :reduce="industry => industry.value" placeholder="Select industry"></v-select>
            </div>

            {{form}}

            <div class="form-group mb-3">
                profile image:
            <input 
                type="file" 
                class="form-control" 
                multiple 
                @change="handleFileUpload"
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
            name: '',
            contact_email: '',
            address: '',
            city: '',
            state: '',
            country: '',
            industry: '', 
        });
        const errors = reactive({});
        const isSubmit = ref(false);
        const router = useRouter();

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

        const fetchCompany = async () => {
            try {
                const { data } = await axios.get(`/api/admin/company/getCompany/${props.id}`);

                Object.keys(form).forEach(key => {
                    if (data.company[key] !== undefined && data.company[key] !== null) {
                        form[key] = data.company[key];
                    }
                });

            } catch (error) {
                console.error("There was an error fetching category:", error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(fetchCompany);

        const submit = async () => {
            if (isSubmit.value) return;
            isSubmit.value = true;

            try {
                await axios.put(`/api/admin/company/updateCompany/${props.id}`, form);

                Swal.fire({
                    title: 'Updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

                router.push('/admin/company');

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    Object.assign(errors, error.response.data.errors);
                } else {
                    console.error('Error updating company:', error);
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
            submit
        };
    }
};
</script>

<style scoped>
/* Add styles as needed */
</style>
