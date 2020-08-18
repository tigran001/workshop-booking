import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
  state: {
    rows: [
        {day: "2020-08-18", startTime: "17:01", endTime: "18:01", maxGuests: "22"}
    ],
  },
  mutations: {
    addRow(state, row) {
      state.rows.push(row);
    },
    editRow(state, data) {
        state.rows[data.id] = data.row;
    },
    deleteRow(state, id) {
        state.rows.splice(id, 1);
    }
  },
  getters: {
    rows: state => state.rows
  }
})