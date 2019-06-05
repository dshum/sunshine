var app = new Vue({
    el: '#app',
    data: {
        selectedCity: '',
        longestSunshinePeriod: null,
        monthMaxSunshinePeriod: null,
        currentSunshinePeriod: null,
        time: null
    },
    created () {
        this.selectedCity = localStorage.getItem('selectedCity');

        if (this.selectedCity) {
            this.getSunshine('/sunshine/' + this.selectedCity);
        }
    },
    methods: {
        getSunshine () {
            if (! this.selectedCity) {
                this.longestSunshinePeriod = null;
                this.monthMaxSunshinePeriod = null;
                this.currentSunshinePeriod = null;
                this.time = null;

                localStorage.setItem('selectedCity', this.selectedCity);

                return false;
            }

            this.$http.get('/sunshine/' + this.selectedCity).then((response) => {
                var data = response.body;
            
                this.longestSunshinePeriod = data.longestSunshinePeriod;
                this.monthMaxSunshinePeriod = data.monthMaxSunshinePeriod;
                this.currentSunshinePeriod = data.currentSunshinePeriod;
                this.time = data.time;

                localStorage.setItem('selectedCity', this.selectedCity);
            });
        }
    }
});