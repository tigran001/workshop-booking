<template>
    <div class="container">
        <div id="workshop-booking">
            <md-table v-model="workshops" md-sort="name" md-sort-order="asc" md-card>
                <md-table-toolbar>
                    <h1 class="md-title">Workshops timetable</h1>
                </md-table-toolbar>

                <md-table-row slot="md-table-row" slot-scope="{ item }">
                    <md-table-cell md-label="Day" md-sort-by="day">{{ item.day }}</md-table-cell>
                    <md-table-cell md-label="Time" md-sort-by="time">{{ item.time }}</md-table-cell>
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
    export default {
        name: "WorkshopBooking",
        data: () => ({
            workshops: [
                {
                    day: 'June 1st',
                    time: '9AM - 12PM',
                    guests: '5',
                },
                {
                    day: 'June 1st',
                    time: '12PM - 3PM',
                    guests: '5',
                },
                {
                    day: 'June 1st',
                    time: '3PM - 6PM',
                    guests: '7',
                },
                {
                    day: 'June 2nd',
                    time: '9AM - 12PM',
                    guests: '5',
                },
            ]
        }),

        computed: {
            ...mapGetters([
                'rows',
            ])
        },

        beforeMount() {
            this.$store.commit('getWorkshops');
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
