import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        rows: [
        ],
    },
    mutations: {
        addRow(state, row) {
            axios.post('/add-workshop', row)
                .then(res => {
                    if(res.data.succsess) {
                        state.rows.push(row);
                    }
                }).catch(err => {
                    console.log(err)
                }
            );
        },
        editRow(state, data) {
            let id = state.rows[data.id].id;
            axios.post(`/edit-workshop/${id}`, data.row)
                .then(res => {
                    if(res.data.succsess) {
                        data.row['id'] = id;
                        state.rows[data.id] = data.row;
                    }                    
                }).catch(err => {
                    console.log(err)
                }
            );
        },
        deleteRow(state, index) {
            axios.delete(`/delete-workshop/${state.rows[index].id}`)
                .then(res => {
                    if(res.data.succsess) {
                        state.rows.splice(index, 1);
                    }                    
                }).catch(err => {
                    console.log(err)
                }
            );
        },
        getWorkshops(state) {
            console.log("getWorkshops -> state", state)
            axios.post('/get-workshop')
                .then(res => {
                    state.rows = res.data.workshops;
                }).catch(err => {
                    console.log(err)
                }
            );
        },
    },
    getters: {
        rows: state => state.rows
    }
})