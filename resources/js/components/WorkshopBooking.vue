<template>
    <div class="container">
        <div id="workshop-booking">
            <md-table v-model="workshops" md-sort="name" md-sort-order="asc" md-card>
                <md-table-toolbar>
                    <h1 class="md-title">Workshops timetable</h1>
                </md-table-toolbar>

                <md-table-row slot="md-table-row" slot-scope="{ item }">
                    <md-table-cell md-label="Day" md-sort-by="day">{{ dayFormat(item.day) }}</md-table-cell>
                    <md-table-cell md-label="Start Time" md-sort-by="start_time">{{ item.start_time }}</md-table-cell>
                    <md-table-cell md-label="End Time" md-sort-by="end_time">{{ item.end_time }}</md-table-cell>
                    <md-table-cell md-label="Max guests" md-sort-by="guests">{{ item.guests }}</md-table-cell>
                    <md-table-cell md-label="Book">
                        <svg @click="openBook(item.id)" class="book-svg" width="30px" height="30px">
                            <use xlink:href="/img/svg/icons.svg#calendar"></use>
                        </svg>
                    </md-table-cell>
                </md-table-row>
            </md-table>
            <md-dialog :md-active.sync="showDialog">
                <div class="card">
                    <div class="card-header">
                        <md-dialog-title>Workshop booking</md-dialog-title>
                    </div>
                    <div class="card-body">
                        <form novalidate class="md-layout" @submit.prevent="validateWorkshop()">
                            <md-card class="md-layout-item">
                                <md-card-content>
                                    <div class="md-layout md-gutter">
                                        <div class="md-layout-item md-small-size-100">
                                            <md-field :class="getValidationClass('name')">
                                                <label for="name">Customer Name</label>
                                                <md-input type="text" id="name" name="name" autocomplete="name" v-model="form.name" :disabled="sending" />
                                                <span class="md-error" v-if="!$v.form.name.required">The name is required</span>
                                            </md-field>
                                        </div>
                                        <div class="md-layout-item md-small-size-100">
                                            <md-field :class="getValidationClass('phone')">
                                                <label for="phone">Phone</label>
                                                <md-input type="number" id="phone" name="phone" autocomplete="phone" v-model="form.phone" :disabled="sending" />
                                                <span class="md-error" v-if="!$v.form.phone.required">The phone is required</span>
                                            </md-field>
                                        </div>
                                    </div>
                                    <md-divider class="my-4"></md-divider>

                                    <md-table v-model="guests" md-sort-order="asc" md-card>
                                        <md-table-row slot="md-table-row" slot-scope="{ item }">
                                            <md-table-cell md-label="Name" md-sort-by="name">{{ item.name }}</md-table-cell>
                                            <md-table-cell md-label="Email" md-sort-by="email">{{ item.email }}</md-table-cell>
                                            <md-table-cell md-label="Delete">
                                                <md-button class="md-icon-button md-accent" v-on:click="deleteRow(item)">
                                                    <svg class="delete-svg"  width="25px" height="25px">
                                                        <use xlink:href="/img/svg/icons.svg#delete"></use>
                                                    </svg>
                                                </md-button>
                                            </md-table-cell>
                                        </md-table-row>
                                    </md-table>
                                    <div class="md-layout md-gutter">
                                        <div class="md-layout-item md-small-size-100">
                                            <md-field :class="getValidationClass('guest_name')">
                                                <label for="guest_name">Guest Name</label>
                                                <md-input type="text" id="guest_name" name="guest_name" autocomplete="guest_name" v-model="form.guest_name" :disabled="sending" />
                                                <span class="md-error" v-if="!$v.form.guest_name.required">The guest_name is required</span>
                                            </md-field>
                                        </div>
                                        <div class="md-layout-item md-small-size-100">
                                            <md-field :class="getValidationClass('guest_email')">
                                                <label for="guest_email">Guest Email</label>
                                                <md-input type="email" id="guest_email" name="guest_email" autocomplete="guest_email" v-model="form.guest_email" :disabled="sending" />
                                                <span class="md-error" v-if="!$v.form.guest_email.required">The guest email is required</span>
                                                <span class="md-error" v-else-if="!$v.form.guest_email.email">Should format email</span>
                                            </md-field>
                                        </div>
                                    </div>
                                    <md-card-actions>
                                        <md-button type="submit" class="md-primary" @click="addingGuest = true" :disabled="sending">Add Guest</md-button>
                                    </md-card-actions>
                                </md-card-content>
                                <md-divider></md-divider>
                                <md-progress-bar md-mode="indeterminate" v-if="sending" />

                                <md-card-actions>
                                    <md-button class="md-primary" @click="closeDialog">Close</md-button>
                                    <md-button type="submit" class="md-primary" @click="addingGuest = false" :disabled="sending">Book</md-button>
                                </md-card-actions>
                            </md-card>
                        </form>
                    </div>
                </div>
            </md-dialog>
        </div>
    </div>
</template>

<script>
    import { validationMixin } from 'vuelidate';
    import { mapGetters } from 'vuex';
    import moment from 'moment';
    import {
        required,
        minValue,
        email
    } from 'vuelidate/lib/validators';
    export default {
        name: "WorkshopBooking",
        mixins: [validationMixin],
        data: () => ({
            form: {
                name: null,
                phone: null,
                guest_name: null,
                guest_email: null,
            },
            guests: [],
            addingGuest: false,
            sending: false,
            showDialog: false,
            workshopId: null,
        }),

        validations() {
            return {
                form: {
                    name: {
                        required: this.addingGuest ? false : required,
                    },
                    phone: {
                        required: this.addingGuest ? false : required,
                    },
                    guest_name: {
                        required: this.addingGuest ? required : false,
                    },
                    guest_email: {
                        required: this.addingGuest ? required : false,
                        email
                    },
                }
            }
        },

        computed: {
            ...mapGetters([
                'rows',
            ]),
            workshops: {
                // getter
                get: function () {
                    return this.rows;
                },
                // setter
                set: function (newValue) {}
            }
        },

        beforeMount() {
            this.$store.commit('getWorkshops');
        },

        methods: {
            getValidationClass (fieldName) {
                const field = this.$v.form[fieldName];
                if (field) {
                    return {
                        'md-invalid': field.$invalid && field.$dirty
                    }
                }
            },
            clearForm () {
                this.$v.$reset();
                this.form.name = null;
            },

            validateWorkshop () {
                this.$v.$touch();
                if (!this.$v.$invalid) {
                    if(this.addingGuest) {
                        this.addGuest();
                    } else {
                        this.book();
                    }
                }
            },

            dayFormat(day, format = 'MMMM Do') {
                return moment(day).format(format);
            },

            openBook(id) {
                this.showDialog = true;
                this.workshopId = id;
            },

            closeDialog(clear) {
                this.showDialog = false;
                this.workshopId = null;
                if(clear) {
                    this.clearForm();
                }
            },

            book() {
                this.closeDialog(true);
            },

            addGuest() {
                this.guests.push({
                    name: this.form.guest_name,
                    email: this.form.guest_email,
                });
                this.$v.$reset();
                this.form.guest_name  = null;
                this.form.guest_email = null;
                this.addingGuest = false;
            },

            clearForm () {
                this.$v.$reset();
                this.form.name         = null;
                this.form.phone        = null;
                this.form.guest_name   = null;
                this.form.guests_email = null;
            },

            deleteRow() {
                
            },
        },
    }
</script>

<style lang="scss">
    #workshop-booking {
        padding-top: 50px;
        .book-svg {
            margin-bottom: -10px;
            cursor: pointer;
        }
    }
    .md-dialog-container {
        width: 50%;
        overflow-y: scroll;
        @media only screen and (max-width:750px) {
            width: 100%;
        }
        .md-card {
            box-shadow: 0 0;
        }
        &::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            border-radius: 10px;
            background-color: #F5F5F5;
        }

        &::-webkit-scrollbar {
            width: 6px;
            background-color: #F5F5F5;
        }

        &::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #cccccc;
        }
    }

    .md-list {
        width: 320px;
        max-width: 100%;
        display: inline-block;
        vertical-align: top;
        border: 1px solid rgba(#000, .12);
    }

</style>
