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
                        <svg class="book-svg" width="30px" height="30px">
                            <use xlink:href="/img/svg/icons.svg#calendar"></use>
                        </svg>
                    </md-table-cell>
                </md-table-row>
            </md-table>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import moment from 'moment';
    export default {
        name: "WorkshopBooking",
        data: () => ({
        }),

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
            
            dayFormat(day, format = 'MMMM Do') {
                return moment(day).format(format);
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

</style>
