<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Zoom Meeting List</h2>

            <button class="btn btn-primary" @click="updateStatus" :disabled="isUpdating">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="isUpdating"></span>
                <i class="fas fa-sync" v-else></i>&nbsp;Update Status
            </button>
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
                            <label class="form-label">Zoom Meeting ID</label>
                            <input type="text" class="form-control" v-model="form.zoom_meeting_id"  @keyup.enter="search">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2" v-if="showSearch">
                <button class="btn btn-danger" @click="resetForm"><i class="fas fa-eraser"></i></button>
                <button class="btn btn-primary" @click="search"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div v-if="!loading && !meetings.length">
            <p v-if="searched">No meetings match your search criteria. Try adjusting the filters.</p>
            <p v-else>No meetings available.</p>
        </div>

        <div v-if="meetings.length && !loading">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Zoom Meeting ID</th>
                        <th scope="col">Applicant Detail</th>
                        <th scope="col">Join URL</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(meeting, index) in meetings" :key="meeting.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>
                            <div>
                                {{meeting.zoom_meeting_id}}
                            </div>
                            <span class="badge bg-info">{{ $moment(meeting.start_time).format('YYYY MMM DD HH:mm A') }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <div>
                                    {{meeting.application.applicant.name}}
                                </div>
                                <div>
                                    {{meeting.application.applicant.email}}
                                </div>
                                <div>
                                    +{{meeting.application.applicant.contact_no}}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                {{meeting.join_url}}
                                <button class="btn btn-secondary btn-sm ms-1" @click="copyToClipboard(meeting.join_url, index, 'join_url')"> <i :class="copyIcon(index, 'join_url')"></i></button>
                            </div>
                        </td>
                        <td>
                            <div class="text-capitalize">
                                {{meeting.status}}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 w-100">
                                <button class="btn btn-primary d-flex align-items-center" @click="startMeeting(meeting.start_url)">
                                    <i class="fas fa-video me-1"></i>Start
                                </button>

                                <button class="btn btn-primary d-flex align-items-center" @click="sendNoti(meeting)" :disabled="loadingId === meeting.id">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" v-if="loadingId === meeting.id"></span>
                                    <i class="fas fa-bell me-1" v-else></i>Notify
                                </button>
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
    name: 'Meeting',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const meetings = ref([]);
        const loading = ref(false);
        const copied = ref({ index: null, field: null });
        const searched = ref(false);
        const showSearch = ref(true);
        const isUpdating = ref(false);
        const loadingId = ref(null);

        const form = reactive({
            zoom_meeting_id: '',
        });

        const resetForm = () => {
            form.zoom_meeting_id = '';
            searched.value = false; 
            getData();
        };

        const getData = async (params = {}, showLoading = true) => {
            if(showLoading) loading.value = true;
            
            try {
                const response = await axios.get('/api/admin/application/getMeeting', { params });
                meetings.value = response.data.meetings;
            } catch (error) {
                console.error("Error fetching meetings:", error);
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

        const startMeeting = async (url) => {
            window.open(url)
        };

        const sendNoti = async (meeting) => {
            if (loadingId.value) return;

            loadingId.value = meeting.id;

            try {
                const response = await axios.post(`/api/admin/application/${meeting.id}/sendMeetingNotification`);

                Swal.fire({
                    title: 'Notification sent',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

            } catch (error) {
                console.error("Error send notifications:", error);
            } finally {
                loadingId.value = null;
            }
        };

        const updateStatus = async() => {
            if (isUpdating.value) return;

            isUpdating.value = true;

            try {
                const response = await axios.post(`/api/admin/manualUpdate`, {});

                Swal.fire({
                    title: 'Updated successful',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

            } catch (error) {
                console.error("Error updating meetings:", error);
            } finally {
                isUpdating.value = false;
            }
        };

        onMounted(() => getData({}, true));

        const search = () => { 
            searched.value = true; 
            getData(form, false); 
        };

        return {
           meetings,
           loading,
           copyToClipboard,
           copyIcon,
           searched,
           showSearch,
           startMeeting,
           sendNoti,
           form,
           resetForm,
           search,
           updateStatus,
           isUpdating,
           loadingId
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
