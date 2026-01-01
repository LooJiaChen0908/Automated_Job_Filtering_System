<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Company List</h2>

            <router-link to="/admin/company/create" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Add New
            </router-link>
        </div>

        <Loading v-if="loading" />

        <div class="card mb-4" v-else>
            <div class="card-header d-flex justify-content-between align-items-center">
                Advanced Search
                <i :class="showSearch ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" style="cursor: pointer;" @click="showSearch = !showSearch"></i>
            </div>

            <div class="card-body" v-if="showSearch">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" v-model="form.name" @keyup.enter="search" placeholder="Enter name">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Industry</label>
                            <v-select :options="industries" v-model="form.industry" label="label" :reduce="industry => industry.value" placeholder="Select industry"></v-select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label">State</label>
                            <v-select :options="states" v-model="form.state" placeholder="Select state"></v-select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Country</label>
                            <v-select :options="countries" v-model="form.country" label="name" :reduce="country => country.id" placeholder="Select country"></v-select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2" v-if="showSearch">
                <button class="btn btn-danger" @click="resetForm"><i class="fas fa-eraser"></i></button>
                <button class="btn btn-primary" @click="search"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div v-if="!loading && !companies.length">
            <p v-if="searched">No companies match your search criteria. Try adjusting the filters.</p>
            <p v-else>No companies available. Please add some.</p>
        </div>

        <div v-if="companies.length && !loading">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Company Detail</th>
                        <th scope="col">Location</th>
                        <th scope="col">Industry</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(company, index) in companies" :key="company.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>
                            <div class="d-flex align-items-center text-capitalize mb-1">
                                {{ company.name }} <button class="btn btn-secondary btn-sm ms-1" @click="copyToClipboard(company.name, index, 'name')"> <i :class="copyIcon(index, 'name')"></i></button>
                            </div>
                            <div v-if="company.contact_email" class="d-flex align-items-center mb-1">
                                {{ company.contact_email }}
                                <button class="btn btn-secondary btn-sm ms-1" @click="copyToClipboard(company.contact_email, index, 'email')"> <i :class="copyIcon(index, 'email')"></i></button>
                            </div>
                            <div v-if="company.contact_number" class="d-flex align-items-center">
                                +{{ company.contact_number }}
                                <button class="btn btn-secondary btn-sm ms-1" @click="copyToClipboard(company.contact_number, index, 'contact_number')"> <i :class="copyIcon(index, 'contact_number')"></i></button>
                            </div>
                        </td>
                        <td>
                            <div class="mb-2" v-if="company.address">
                                Address: {{company.address}}
                            </div>

                            <div v-if="company.state && company.city">
                                {{ company.state }}, {{ company.city }}
                            </div>

                            {{ company.country_name }}

                            <div v-if="company.image_paths && company.image_paths.length" class="mt-2">
                                <img
                                    v-for="(img, i) in company.image_paths"
                                    :key="i"
                                    :src="`/storage/${img}`"
                                    alt="Company Image"
                                    style="width: 80px; height: 80px; object-fit: cover; margin-right: 5px; border-radius: 6px;"
                                />
                            </div>
                        </td>
                        <td>{{ company.industry_name }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-success" @click="editCompany(company)"><i class="fas fa-edit"></i>&nbsp;Edit</button>
                                <button class="btn btn-danger" @click="deleteCompany(company)"><i class="fas fa-trash-alt"></i>&nbsp;Delete</button>
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
    name: 'Company',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const companies = ref([]);
        const loading = ref(false);
        const copied = ref({ index: null, field: null });
        const searched = ref(false);
        const showSearch = ref(true);

        const form = reactive({
            name: '',
            country: '',
            state: '',
            industry: '',
        });

        const resetForm = () => {
            form.name = '';
            form.country = '';
            form.state = '',
            form.industry = '';
            searched.value = false; 
            getData();
        };

        const countries = ref([
            { id: 'MY', name: 'Malaysia' },
            { id: 'SG', name: 'Singapore' },
            { id: 'TH', name: 'Thailand' },
            { id: 'ID', name: 'Indonesia' },
            { id: 'PH', name: 'Philippines' },
            { id: 'VN', name: 'Vietnam' },
            { id: 'CN', name: 'China' }
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

        // Common Malaysian cities (you can add more)
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

        const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/admin/company');
                companies.value = response.data.data;
            } catch (error) {
                console.error("There was an error fetching companies:", error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(getData);

        const deleteCompany = async (company) => {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete ${company.name || ''}!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/admin/company/deleteCompany/${company.id}`);
                    Swal.fire({
                    title: 'Deleted successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                    });
                    await getData();
                } catch (error) {
                    console.error("There was an error deleting the company:", error);
                }
            }
        };

        const editCompany = async (company) => {
            router.push({ name: 'EditCompany', params: { id: company.id }});
        };

        const copyToClipboard = async (text, index, field) => {
            try {
                await navigator.clipboard.writeText(text);
                copied.value = { index, field };
                setTimeout(() => {
                    copied.value = { index: null, field: null };
                }, 2000);
            } catch (err) {
                console.error('Failed to copy: ', err);
            }
        };

        const copyIcon = (index, field) => {
            return copied.value.index === index && copied.value.field === field ? 'fas fa-copy' : 'far fa-copy';
        };

        const openImageModal = async () => {
            // open modal
        };

        const search = async () => {
            searched.value = true;

            try {
                const response = await axios.get('/api/admin/company/search', {
                    params: {
                        name: form.name,
                        state: form.state,
                        country: form.country,
                        industry: form.industry,
                    }
                });

                companies.value = response.data.companies;

            } catch (error) {
                console.error("Error fetching company", error);
            }
        };
        
        return {
           companies,
           loading,
           editCompany,
           deleteCompany,
           copyToClipboard,
           copyIcon,
           openImageModal,
           search,
           form,
           resetForm,
           searched,
           states,
           countries,
           industries,
           showSearch
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
