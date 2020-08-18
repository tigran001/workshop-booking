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
                            <span class="md-error" v-else-if="!$v.form.day.minValue">Please select relevent date</span>
                        </md-datepicker>
                    </div>
                    <div class="md-layout-item md-small-size-100">
                        <md-field :class="getValidationClass('guests')">
                            <label for="guests">Max Guests</label>
                            <md-input type="number" id="guests" name="guests" autocomplete="guests" v-model="form.guests" :disabled="sending" />
                            <span class="md-error" v-if="!$v.form.guests.required">The guests is required</span>
                        </md-field>
                    </div>
                </div>

                <div class="md-layout md-gutter">
                    <div class="md-layout-item md-small-size-100">
                        <md-field :class="getValidationClass('start_time')">
                            <label for="start-time">Start Time</label>
                            <md-input type="time" name="start-time" id="start-time" autocomplete="start_time" v-model="form.start_time" :disabled="sending" />
                            <span class="md-error" v-if="!$v.form.start_time.required">The start time is required</span>
                            <span class="md-error" v-else-if="!$v.form.start_time.minValue">You can't choose time before current time</span>
                        </md-field>
                    </div>
                    <div class="md-layout-item md-small-size-100">
                        <md-field :class="getValidationClass('end_time')">
                            <label for="end-time">End Time</label>
                            <md-input type="time" name="end-time" id="end-time" autocomplete="end_time" v-model="form.end_time" :disabled="sending" />
                            <span class="md-error" v-if="!$v.form.end_time.required">The end time is required</span>
                            <span class="md-error" v-else-if="!$v.form.end_time.minValue">End time should follow start time</span>
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
      <md-table class="workshop" v-model="workshops" md-sort="name" md-sort-order="asc" md-card>
        <md-table-row slot="md-table-row" slot-scope="{ item }">
          <md-table-cell md-label="Day" md-sort-by="day">{{ dayFormat(item.day) }}</md-table-cell>
          <md-table-cell md-label="Start Time" md-sort-by="start_time">{{ item.start_time }}</md-table-cell>
          <md-table-cell md-label="End Time" md-sort-by="end_time">{{ item.end_time }}</md-table-cell>
          <md-table-cell md-label="Max guests" md-sort-by="guests">{{ item.guests }}</md-table-cell>
          <md-table-cell>
              <div class="d-flex">
                <md-button class="md-icon-button md-accent" v-on:click="editRow(item.id)">
                    <svg class="edit-svg">
                        <use xlink:href="/img/svg/icons.svg#edit"></use>
                    </svg>
                </md-button>
                <md-button class="md-icon-button md-accent" v-on:click="deleteRow(item.id)">
                    <svg class="delete-svg"  width="25px" height="25px">
                        <use xlink:href="/img/svg/icons.svg#delete"></use>
                    </svg>
                </md-button>
              </div>
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
    required,
    minValue
  } from 'vuelidate/lib/validators';
  export default {
    name: 'AddWorkshop',
    mixins: [validationMixin],
    data: () => ({
      form: {
        day: null,
        start_time: null,
        end_time: null,
        guests: null,
      },
      workshopSaved: false,
      sending: false,
      lastWorkshop: null,
      editWorkshop: false,
      workshopId: null
    }),
    validations() {
        return{
            form: {
              day: {
                required,
                minValue: value => moment().diff(value, 'day') <= 0,
              },
              start_time: {
                required,
                minValue: value => moment().diff(this.form.day, 'day') === 0 ?
                    moment().diff(`${this.form.day.toString().split('00:00:00')[0]} ${value}`, 'minutes') < 0
                    : true,
              },
              end_time: {
                required,
                minValue: value => value > this.form.start_time,
              },
              guests: {
                required,
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
            const field = this.$v.form[fieldName]
            if (field) {
            return {
                'md-invalid': field.$invalid && field.$dirty
            }
            }
        },
        clearForm () {
            this.$v.$reset();
            this.form.day        = null;
            this.form.start_time = null;
            this.form.end_time    = null;
            this.form.guests  = null;
        },
        saveWorkshop () {
            this.sending = true
            window.setTimeout(() => {
                this.lastWorkshop  = `${this.dayFormat(this.form.day)} ${this.form.start_time} - ${this.form.end_time}`;
                this.workshopSaved = true;
                this.sending       = false;
                let row = {
                    day: this.form.day,
                    start_time: this.form.start_time,
                    end_time: this.form.end_time,
                    guests: this.form.guests
                };
                this.$store.commit('addRow', row);
                this.clearForm();
            }, 1000)
        },
        editWorkshopData (id) {
            this.sending = true
            // Instead of this timeout, here you can call your API
            window.setTimeout(() => {
                this.lastWorkshop  = `${this.dayFormat(this.form.day)} ${this.form.start_time} - ${this.form.end_time}`;
                this.workshopSaved = true;
                this.sending   = false;
                let data = {
                    row: {
                    day: this.form.day,
                    start_time: this.form.start_time,
                    end_time: this.form.end_time,
                    guests: this.form.guests
                    },
                    id: id
                }
                this.$store.commit('editRow', data, id);
                this.clearForm();
                this.editWorkshop = false;
                this.workshopId   = null;
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
            let index         = this.findRowById(id);
            this.form         = this.workshops[index];
            this.editWorkshop = true;
            this.workshopId   = index;
            window.scrollTo(0, 0);
        },
        deleteRow (id) {
            let index = this.findRowById(id);
            if(confirm("Are you sure delete this workshop?")){
                this.$store.commit('deleteRow', index);
            }
        },
        dayFormat(day, format = 'MMMM Do') {
            return moment(day).format(format);
        },

        findRowById(id) {
            return this.workshops.map(item => item.id).indexOf(id);
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
