<template>
    <a class="db-table-action qr-code" :href="this.$props.link" download>
        <i class="fa fa-qrcode"></i>
        <span class="db-tooltip">{{ $t("button.qr") }}</span>
    </a>
    <!--sm warning modal-btn m-0.5-->
</template>

<script>
export default {
    name: "SmIconQrCodeComponent",
    props: {
        link: String
    }
}
</script>

<style scoped></style>
