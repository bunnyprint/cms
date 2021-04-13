<template id="form-job-ticket-template">
    <div class="modal" id="single_modal">
        <div class="modal-dialog modal-lg">
            <form action="#" @submit.prevent="onSubmit" method="POST" autocomplete="off">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <div class="modal-title">
                        @{{form.id ? 'Edit Job Ticket' : 'New Job Ticket'}}
                    </div>
                    <button type="button" class="close" @click="closeModal('single_modal')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label required">
                                Status
                            </label>
                            <multiselect
                                v-model="form.status"
                                :options="statuses"
                                :close-on-select="true"
                                placeholder="Select..."
                                :custom-label="customLabelName"
                                track-by="id"
                            ></multiselect>
                            <span class="invalid-feedback" role="alert" v-if="formErrors['status']">
                                <strong>@{{ formErrors['status'][0] }}</strong>
                            </span>
                        </div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <div class="form-row">
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label required">
                                        Doc No
                                    </label>
                                    <input type="text" name="doc_no" class="form-control" v-model="form.doc_no" :class="{ 'is-invalid' : formErrors['doc_no'] }">
                                    <span class="invalid-feedback" role="alert" v-if="formErrors['doc_no']">
                                        <strong>@{{ formErrors['doc_no'][0] }}</strong>
                                    </span>
                                </div>
                                <div class="form-group col-md-6 col-sm-6 col-xs-12">
                                    <label class="control-label required">
                                        Doc Date
                                    </label>
                                    <datepicker
                                        name="doc_date"
                                        v-model="form.doc_date"
                                        format="yyyy-MM-dd"
                                        :monday-first="true"
                                        :bootstrap-styling="true"
                                        placeholder="Date From"
                                        autocomplete="off"
                                        @input=onDateChanged('doc_date')
                                        >
                                    </datepicker>
                                    <span class="invalid-feedback" role="alert" v-if="formErrors['doc_date']">
                                        <strong>@{{ formErrors['doc_date'][0] }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-row text-center">
                                <h4>
                                    <span class="badge badge-primary">
                                        Customer
                                    </span>
                                </h4>
                            </div>
                            <div class="form-row pt-2">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" value="true" v-model="radioOption.existingCustomer" @change="resetObject('existingCustomer')">
                                        <label class="form-check-label">Existing Customer</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" value="false" v-model="radioOption.existingCustomer" @change="resetObject('existingCustomer')">
                                        <label class="form-check-label">Create New Customer</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-12 col-sm-12 col-xs-12" v-show="radioOption.existingCustomer === 'true'">
                                    <multiselect
                                    v-model="form.customer"
                                    :options="customers"
                                    :close-on-select="true"
                                    placeholder="Select..."
                                    :custom-label="customLabelCodeName"
                                    track-by="id"
                                    ></multiselect>
                                </div>

                                <div class="form-group col-md-12 col-sm-12 col-xs-12" v-show="radioOption.existingCustomer === 'false'">
                                    <div class="pt-3">
                                        <div class="form-row">
                                            <div class="form-group col-md-6 col-xs-6 col-xs-12">
                                                <label class="control-label required">
                                                    Code
                                                </label>
                                                <input type="text" name="customer_code" class="form-control" v-model="form.customer_code" :class="{ 'is-invalid' : formErrors['customer_code'] }">
                                                <span class="invalid-feedback" role="alert" v-if="formErrors['customer_code']">
                                                    <strong>@{{ formErrors['customer_code'][0] }}</strong>
                                                </span>
                                            </div>
                                            <div class="form-group col-md-6 col-xs-6 col-xs-12">
                                                <label class="control-label required">
                                                    Name
                                                </label>
                                                <input type="text" name="customer_name" class="form-control" v-model="form.customer_name" :class="{ 'is-invalid' : formErrors['customer_name'] }">
                                                <span class="invalid-feedback" role="alert" v-if="formErrors['customer_name']">
                                                    <strong>@{{ formErrors['customer_name'][0] }}</strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="form-row text-center">
                                <h4>
                                    <span class="badge badge-primary">
                                        Product
                                    </span>
                                </h4>
                            </div>
                            <div class="form-row pt-2">
                                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" value="true" v-model="radioOption.existingProduct" @change="resetObject('existingProduct')">
                                        <label class="form-check-label">Existing Product</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" value="false" v-model="radioOption.existingProduct" @change="resetObject('existingProduct')">
                                        <label class="form-check-label">Create New Product</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-12 col-sm-12 col-xs-12" v-show="radioOption.existingProduct === 'true'">
                                    <multiselect
                                    v-model="form.product"
                                    :options="products"
                                    :close-on-select="true"
                                    placeholder="Select..."
                                    :custom-label="customLabelCodeName"
                                    track-by="id"
                                    ></multiselect>
                                </div>

                                <div class="form-group col-md-12 col-sm-12 col-xs-12" v-show="radioOption.existingProduct === 'false'">
                                    <div class="pt-3">
                                        <div class="form-row">
                                            <div class="form-group col-md-6 col-xs-6 col-xs-12">
                                                <label class="control-label required">
                                                    Code
                                                </label>
                                                <input type="text" name="product_code" class="form-control" v-model="form.product_code" :class="{ 'is-invalid' : formErrors['product_code'] }">
                                                <span class="invalid-feedback" role="alert" v-if="formErrors['product_code']">
                                                    <strong>@{{ formErrors['product_code'][0] }}</strong>
                                                </span>
                                            </div>
                                            <div class="form-group col-md-6 col-xs-6 col-xs-12">
                                                <label class="control-label required">
                                                    Name
                                                </label>
                                                <input type="text" name="product_name" class="form-control" v-model="form.product_name" :class="{ 'is-invalid' : formErrors['product_name'] }">
                                                <span class="invalid-feedback" role="alert" v-if="formErrors['product_name']">
                                                    <strong>@{{ formErrors['product_name'][0] }}</strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label required">
                                Qty
                            </label>
                            <input type="text" name="number" class="form-control" v-model="form.qty" :class="{ 'is-invalid' : formErrors['qty'] }">
                            <span class="invalid-feedback" role="alert" v-if="formErrors['qty']">
                                <strong>@{{ formErrors['qty'][0] }}</strong>
                            </span>
                        </div>

                        <div class="form-group col-md-12 col-sm-12 col-xs-12">
                            <label class="control-label">
                                Remarks
                            </label>
                            <textarea name="remarks" class="form-control" v-model="form.remarks" rows="4"></textarea>
                            <span class="invalid-feedback" role="alert" v-if="formErrors['qty']">
                                <strong>@{{ formErrors['qty'][0] }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                      <button type="submit" class="btn btn-success" v-if="!form.id">Create</button>
                      <button type="submit" class="btn btn-success" v-if="form.id">Save</button>
                      <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Close</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
  </template>