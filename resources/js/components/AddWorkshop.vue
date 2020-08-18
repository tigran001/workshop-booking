<template>
  <div id="add-workshop" class="content">
    <form novalidate class="md-layout" @submit.prevent="validateWorkshop(workshopId)">
        <md-card class="md-layout-item md-size-50 md-small-size-100">
            <md-card-header>
                <div class="md-title">Workshop</div>
            </md-card-header>
            <md-card-content>
                <div class="md-layout md-gutter">
                    <div class="md-layout-item md-small-size-100">
                        <md-datepicker :class="getValidationClass('day')" name="day" id="day" autocomplete="given-name" v-model="form.day" :disabled="sending">
                            <label for="day">Day</label>
                            <span class="md-error" v-if="!$v.form.day.required">The Day is required</span>
                            <span class="md-error" v-else-if="!$v.form.day.minlength">Invalid Day</span>
                        </md-datepicker>
                    </div>
                    <div class="md-layout-item md-small-size-100">
                        <md-field :class="getValidationClass('maxGuests')">
                            <label for="maxGuests">Max Guests</label>
                            <md-input type="number" id="maxGuests" name="maxGuests" autocomplete="maxGuests" v-model="form.maxGuests" :disabled="sending" />
                            <span class="md-error" v-if="!$v.form.maxGuests.required">The maxGuests is required</span>
                            <span class="md-error" v-else-if="!$v.form.maxGuests.maxlength">Invalid maxGuests</span>
                        </md-field>
                    </div>
                </div>

                <div class="md-layout md-gutter">
                    <div class="md-layout-item md-small-size-100">
                        <md-field :class="getValidationClass('startTime')">
                            <label for="start-time">Start Time</label>
                            <md-input type="time" name="start-time" id="start-time" autocomplete="startTime" v-model="form.startTime" :disabled="sending" />
                            <span class="md-error" v-if="!$v.form.startTime.required">The start time is required</span>
                            <span class="md-error" v-else-if="!$v.form.startTime.minlength">Invalid start Time</span>
                        </md-field>
                    </div>
                    <div class="md-layout-item md-small-size-100">
                        <md-field :class="getValidationClass('endTime')">
                            <label for="end-time">end Time</label>
                            <md-input type="time" name="end-time" id="end-time" autocomplete="endTime" v-model="form.endTime" :disabled="sending" />
                            <span class="md-error" v-if="!$v.form.endTime.required">The end time is required</span>
                            <span class="md-error" v-else-if="!$v.form.endTime.minlength">Invalid end Time</span>
                        </md-field>
                    </div>
                </div>

                
            </md-card-content>

            <md-progress-bar md-mode="indeterminate" v-if="sending" />

            <md-card-actions>
                <md-button type="submit" class="md-primary" :disabled="sending">{{editWorkshop ? "Edit" : "Add Workshop"}}</md-button>
            </md-card-actions>
        </md-card>

      <md-snackbar :md-active.sync="workshopSaved">The workshop {{ lastWorkshop }} was saved with success!</md-snackbar>
    </form>
    <div class="workshops">
      <md-table class="workshop">
        <md-table-row>
          <md-table-head>Day</md-table-head>
          <md-table-head>Start Time</md-table-head>
          <md-table-head>End Time</md-table-head>
          <md-table-head>Max Guests</md-table-head>
          <md-table-head>Actions</md-table-head>
        </md-table-row>
        <md-table-row v-for="(row, index) in rows" :key="index">
          <md-table-cell>{{ dayFormat(row.day) }}</md-table-cell>
          <md-table-cell>{{row.startTime}}</md-table-cell>
          <md-table-cell>{{row.endTime}}</md-table-cell>
          <md-table-cell>{{row.maxGuests}}</md-table-cell>
          <md-table-cell>
            <md-button class="md-icon-button md-accent" v-on:click="editRow(index)">
                <svg class="edit-svg">
                    <use xlink:href="/img/svg/icons.svg#edit"></use>
                </svg>
            </md-button>
            <md-button class="md-icon-button md-accent" v-on:click="deleteRow(index)">
                <svg class="delete-svg"  width="25px" height="25px">
                    <use xlink:href="/img/svg/icons.svg#delete"></use>
                </svg>
            </md-button>
          </md-table-cell>
        </md-table-row>
      </md-table>
    </div>
  </div>
</template>

<script>
  import { validationMixin } from 'vuelidate';
  import { mapGetters } from 'vuex';
  import moment from 'moment';
  import {
    required
  } from 'vuelidate/lib/validators';
  export default {
    name: 'AddWorkshop',
    mixins: [validationMixin],
    data: () => ({
      form: {
        day: null,
        startTime: null,
        endTime: null,
        maxGuests: null,
      },
      workshopSaved: false,
      sending: false,
      lastWorkshop: null,
      editWorkshop: false,
      workshopId: null
    }),
    validations: {
      form: {
        day: {
          required
        },
        startTime: {
          required,
        },
        endTime: {
          required
        },
        maxGuests: {
          required,
        },
      }
    },
    computed: {
        ...mapGetters([
        'rows',
        ])
    },
    methods: {
        getValidationClass (fieldName) {
            const field = this.$v.form[fieldName]
            if (field) {
            return {
                'md-invalid': field.$invalid && field.$dirty
            }
            }
        },
        clearForm () {
            this.$v.$reset();
            this.form.day       = null;
            this.form.startTime = null;
            this.form.endTime   = null;
            this.form.maxGuests = null;
        },
        saveWorkshop () {
            this.sending = true
            // Instead of this timeout, here you can call your API
            window.setTimeout(() => {
            this.lastWorkshop  = `${this.dayFormat(this.form.day)} ${this.form.startTime} - ${this.form.endTime}`;
            this.workshopSaved = true;
            this.sending   = false;
            let row = {
                day: this.form.day,
                startTime: this.form.startTime,
                endTime: this.form.endTime,
                maxGuests: this.form.maxGuests
            };
            this.$store.commit('addRow', row);
            this.clearForm();
            }, 1000)
        },
        editWorkshopData (id) {
            this.sending = true
            // Instead of this timeout, here you can call your API
            window.setTimeout(() => {
            this.lastWorkshop  = `${this.dayFormat(this.form.day)} ${this.form.startTime} - ${this.form.endTime}`;
            this.workshopSaved = true;
            this.sending   = false;
            let data = {
                row: {
                day: this.form.day,
                startTime: this.form.startTime,
                endTime: this.form.endTime,
                maxGuests: this.form.maxGuests
                },
                id: id
            }
            this.$store.commit('editRow', data, id);
            this.clearForm();
            this.editWorkshop = false;
            this.workshopId       = null;
            }, 1000)
        },
        validateWorkshop (id = null) {
            this.$v.$touch();
            if (!this.$v.$invalid) {
            if(this.editWorkshop){
                this.editWorkshopData(id);
            } else {
                this.saveWorkshop();
            }
            }
        },
        editRow (id) {
            this.$v.$reset();
            this.form = this.rows[id];
            this.editWorkshop   = true;
            this.workshopId         = id;
            window.scrollTo(0, 0);
        },
        deleteRow (id) {
            if(confirm("Are you sure delete this workshop?")){
            this.$store.commit('deleteRow', id);
            }
        },
        dayFormat(day) {
            return moment(day).format('MMMM Do YYYY');
        },
    }
  }
</script>

<style lang="scss" scoped>
    #add-workshop {
        label {
            min-width: 50px;
            background: #fff;
        }
        .md-progress-bar {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
        }
        .edit-svg {
            width: 25px;
            height: 25px;
            margin-left: 3px;
            margin-top: 3px;
        }
        .md-card {
            margin: 0 auto;
        }
        .content {
            margin: 20px 0;
        }
    }
</style>
