<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>User List</h2>
        </div>

        <Loading v-if="loading" />

        <div class="card mb-4" v-else>
            <div class="card-header d-flex align-items-center justify-content-between">
                Advanced Search
                <i :class="showSearch ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" style="cursor: pointer;" @click="showSearch = !showSearch"></i>
            </div>

            <div class="card-body" v-if="showSearch">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" v-model="form.name"  @keyup.enter="search">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" v-model="form.contact_no"  @keyup.enter="search">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" v-model="form.email"  @keyup.enter="search">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Specialization</label>
                            <v-select :options="specializations" v-model="form.specialization" label="label" :reduce="option => option.value" placeholder="Select Specialization"></v-select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2" v-if="showSearch">
                <button class="btn btn-danger" @click="resetForm"><i class="fas fa-eraser"></i></button>
                <button class="btn btn-primary" @click="search"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div v-if="!loading && !users.length">
            <p v-if="searched">No users match your search criteria. Try adjusting the filters.</p>
            <p v-else>No users available.</p>
        </div>

        <div v-if="users.length && !loading">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">User Detail</th>
                        <th scope="col">Specialization</th>
                        <th scope="col">Expected Salary</th>
                        <th scope="col">Work experience</th>
                        <th scope="col">Preferred Work Types</th>
                        <th scope="col">Country</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in users" :key="user.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <div>
                                    {{user.name}}
                                    <button class="btn btn-secondary btn-sm ms-1" @click="copyToClipboard(user.name, index, 'name')"> <i :class="copyIcon(index, 'name')"></i></button>
                                </div>
                                <div>
                                    {{user.email}}
                                    <button class="btn btn-secondary btn-sm ms-1" @click="copyToClipboard(user.email, index, 'email')"> <i :class="copyIcon(index, 'email')"></i></button>
                                </div>
                                <div v-if="user.contact_no">
                                    +{{user.contact_no}}
                                    <button class="btn btn-secondary btn-sm ms-1" @click="copyToClipboard(user.contact_no, index, 'contact_no')"> <i :class="copyIcon(index, 'contact_no')"></i></button>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{user.specialization_name ?? '-'}}
                        </td>
                        <td>
                            <div v-if="user.expected_salary">
                                RM{{user.expected_salary}}
                            </div>
                            <div v-else>
                                -
                            </div>
                        </td>
                        <td>
                            <div v-if="user.work_experience">
                                {{user.work_experience}} years
                            </div>
                            <div v-else>
                                -
                            </div>
                        </td>
                        <td>  
                            <div v-if="user.preferred_work_types && user.preferred_work_types.length">
                                <div class="text-capitalize" v-for="(type,index) in user.preferred_work_types" :key="index">
                                    {{type}}
                                </div>
                            </div>
                            <div v-else>
                                -
                            </div>
                        </td>
                        <td>
                            {{user.country ?? '-'}}
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
    name: 'User',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const users = ref([]);
        const loading = ref(false);
        const copied = ref({ index: null, field: null });
        const searched = ref(false);
        const specializations = ref([]);
        const showSearch = ref(true);

        const form = reactive({
            name: '',
            contact_no: '',
            email: '',
            specialization: '',
        });

        const resetForm = () => {
            form.name = '';
            form.contact_no = '';
            form.email = '',
            form.specialization = '';
            searched.value = false; 
            getData();
        };

        const getData = async (params = {}, showLoading = true) => {
            if(showLoading) loading.value = true;
            
            try {
                const response = await axios.get('/api/admin/getUser', { params });
                users.value = response.data.users;
                specializations.value = response.data.specializations;
            } catch (error) {
                console.error("Error fetching users:", error);
            } finally {
                loading.value = false;
            }
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

        onMounted(() => getData({}, true));

        const search = () => { 
            searched.value = true; 
            getData(form, false); 
        };

        return {
           users,
           loading,
           copyToClipboard,
           copyIcon,
           form,
           resetForm,
           search,
           searched,
           specializations,
           showSearch
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
