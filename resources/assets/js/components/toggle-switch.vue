
<template>
    <div class="toggle-switch" @click="switch" :class="{'active': isActive}">
        <input type="hidden" :name="name" :value="value"/>
        <div class="switch-handle"></div>
    </div>
</template>


<script>
    module.exports = {
        props: ['name', 'value'],
        data: function() {
            return {
                isActive: this.value == true && this.value != 'false'
            }
        },
        ready: function() {
            this.value = (this.value == true && this.value != 'false') ? 'true' : 'false';
        },
        methods: {
            switch: function() {
                this.isActive = !this.isActive;
                this.value = this.isActive ? 'true' : 'false';
            }
        }
    };
</script>