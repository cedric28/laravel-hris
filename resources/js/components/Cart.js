import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import http from "../services/httpService"
import Select from 'react-select';
import Swal from "sweetalert2";
import _ from "lodash";

class Cart extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            products: [],
            inventory_product: [],
            normal_discounts: [],
            point_discount: [],
            customer_point_info: {},
            customer_point_earner: {},
            customer_earner_balance: "",
            discount: [],
            item_quantity: '',
            cash_tendered: '',
            selectedNormalDiscount: false,
            selectedCustomerDiscount: false,
            customerFullName: "",
            customerReferenceNo: "",
        };

        this.loadCart = this.loadCart.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.loadProducts = this.loadProducts.bind(this);
        this.loadNormalDiscounts = this.loadNormalDiscounts.bind(this);
        this.loadPointDiscount = this.loadPointDiscount.bind(this);
        this.setInventoryId = this.setInventoryId.bind(this);
        this.setQuantity  = this.setQuantity.bind(this);
        this.handleSelectCustomerPoint = this.handleSelectCustomerPoint.bind(this);
        this.handleSelectNormalDiscount = this.handleSelectNormalDiscount.bind(this);
        this.handleAddToCart = this.handleAddToCart.bind(this);
        this.setCashTendered = this.setCashTendered.bind(this);
        this.setCustomerFullName = this.setCustomerFullName.bind(this);
        this.setCustomerReferenceNo = this.setCustomerReferenceNo.bind(this);
        this.handleCheckCustomer = this.handleCheckCustomer.bind(this);
        this.handleSubmitOrder = this.handleSubmitOrder.bind(this);
        this.handleResetCart = this.handleResetCart.bind(this);
        this.handleKeyDown = this.handleKeyDown.bind(this);
    }

    componentDidMount() {
        // load user cart
        this.loadCart();
        this.loadProducts();
        this.loadNormalDiscounts();
        this.loadPointDiscount();

        window.addEventListener('keydown', this.handleKeyDown);

        // cleanup this component
        return () => {
          window.removeEventListener('keydown', this.handleKeyDown);
        };
    }

    handleKeyDown (event) {
       if(event.keyCode == 116){
           this.handleSubmitOrder();
       } else if(event.keyCode == 117){
           this.handleResetCart();
       }
    }

    loadProducts() {
        http.get('inventories/fetch/q').then(res => {
            const products = res.data.inventories;
            this.setState({ products });
        });
    }

    loadNormalDiscounts() {
        http.get('normal-discounts/fetch/q').then(res => {
            const normal_discounts = res.data.normalDiscounts;
            this.setState({ normal_discounts });
        });
    }

    loadPointDiscount() {
        http.get('customer-points/fetch/q').then(res => {
            const point_discount = res.data.customerPoints;
            this.setState({ point_discount });
        });
    }

    loadCart(){
        // const { cart } = this.state;
        // localStorage.setItem("cart", JSON.stringify(cart));
    }

    setInventoryId(value) {
        this.setState({ inventory_product: value });
    }

    setQuantity(event){
        console.log(event.target.value)
        this.setState({ item_quantity: event.target.value });
    }

    setCashTendered(event){
        this.setState({ cash_tendered : event.target.value });
    }

    setCustomerFullName(event){
        this.setState({ customerFullName : event.target.value });
    }

    setCustomerReferenceNo(event)
    {
        this.setState({ customerReferenceNo : event.target.value });
    }

    handleCheckCustomer(event)
    {
        event.preventDefault();
        let { customerReferenceNo, customer_point_earner, customer_earner_balance, customerFullName } = this.state;
        if (!customerReferenceNo) {
            let error = 'Customer Reference No field is required';
            Swal.fire("Error!", error, "error");
        }

        http.post('/check-customer-info/fetch/q', {reference_no: customerReferenceNo, account_type: 'earner'}).then(res => {
            if(res.data.status == "success"){
                let tempCustomerInfo = {...customer_point_earner};
                let customerBalance = res.data.balance;
                tempCustomerInfo = res.data.customerInfo;

                this.setState({
                    customer_point_earner: tempCustomerInfo,
                    customer_earner_balance: customerBalance,
                    customerFullName: tempCustomerInfo.name
                })
            }
        }).catch(err => {
            let tempCustomerInfo = {...customer_point_earner};
            tempCustomerInfo = {};
            this.setState({
                customerReferenceNo: "",
                customer_point_earner: tempCustomerInfo,
                customer_earner_balance: ""
            })
            Swal.fire("Error!", err.response.data.message, "error");
        })
    }   

    handleSelectCustomerPoint(e)
    {
        const checked = e.target.checked;
        if(checked){
            let tempDiscount = {...this.state.discount};
            this.setState({
                selectedNormalDiscount: false,
                selectedCustomerDiscount: true,
            })

            Swal.fire({
                title: 'Enter Customer Reference No',
                input: 'number',
                inputValidator: (value) => {
                    if (!value) {
                      return 'Please Enter Customer Reference No.!'
                    }
                },
                showCancelButton: true,
                confirmButtonText: 'Send',
                showLoaderOnConfirm: true,
                preConfirm: (reference_no) => {
                    return http.post('/check-customer-info/fetch/q', {reference_no: reference_no, account_type: 'discount'}).then(res => {
                       if(res.data.status == "error"){
                            Swal.showValidationMessage(`${res.data.message}<br/> Your Balance: ${res.data.balance} points`);
                       } else {
                            return res.data;
                       }
                    }).catch(err => {
                        Swal.showValidationMessage(err.response.data.message)
                    })
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    const { customerInfo, discount } = result.value;
                    let tempCustomerInfo = {...this.state.customer_point_info};
                    tempCustomerInfo = customerInfo;
                    tempDiscount = discount[0];
                    this.setState({
                        customer_point_info: tempCustomerInfo,
                        discount: tempDiscount
                    })
                }else if(result.dismiss == 'cancel'){
                    tempDiscount = [];
                    this.setState({
                        selectedCustomerDiscount: false,
                        discount: tempDiscount,
                    })
                 }
            });
        }
    }

    handleSelectNormalDiscount(e)
    {
        const { normal_discounts, discount, customer_point_info } = this.state;
        let tempDiscount = {...discount};
        let tempCustomerInfo  = {...customer_point_info};
        const checked = e.target.checked;
        const normalDiscountId = e.target.value;
        if(checked){
            const discountItem = normal_discounts.find(item => item.id == normalDiscountId );
            tempDiscount = discountItem;
            if(Object.keys(customer_point_info).length > 0){
                tempCustomerInfo = {};
            }
            this.setState({
                selectedNormalDiscount: true,
                selectedCustomerDiscount: false,
                discount : tempDiscount,
                customer_point_info: tempCustomerInfo
            })
        }
    }

    getTotalDiscount(){
        const { discount } = this.state;
        const totalDiscount = discount.length != 0 ? this.getTotal() - (this.getTotal() * parseFloat(discount.discount_rate)) : 0;
        console.log("Total Discount", totalDiscount)
        console.log("Discount", discount)
        return totalDiscount.toFixed(2);
    }

    getTotalAmountDue(){
        const { discount } = this.state;
        const total_amount_due = discount.length != 0 ? this.getTotal() - (this.getTotal() * parseFloat(discount.discount_rate)) : this.getTotal();
        return total_amount_due;
    }

    getTotal() {
        const { cart } = this.state;
        
        return cart.reduce((total,product) => {
            return total + (product.selling_price * product.item_quantity);
        },0);
        
    }

    handleAddToCart(event){
        event.preventDefault();
        let { inventory_product, item_quantity, cart, products } = this.state;
        if (isNaN(item_quantity)) {
            let error = 'Quantity can only be number';
            Swal.fire("Error!", error, "error");
        }

        if(inventory_product.length === 0){
            let error = 'Products field cannot be empty';
            Swal.fire("Error!", error, "error");
        } else if(!item_quantity){
            let error = 'Quantity field cannot be empty';
            Swal.fire("Error!", error, "error");
        } else if(inventory_product.quantity < item_quantity){
            let error = `Insufficient Quantity!`;
            Swal.fire("Error!", error, "error");
        } else {
            let tempCart = [...cart];
            const tempProducts = [...products];
            let productItem = tempProducts.find(item => item.id == inventory_product.id );
            let cartItems = tempCart.find(item => item.id == inventory_product.id );
            let qty = _.parseInt(item_quantity) ? _.parseInt(item_quantity) : 1;
            if(cartItems){
                cartItems.item_quantity += qty;
                productItem.quantity -= qty;
                this.setState({
                    cart : tempCart,
                    products: tempProducts
                })
               
            } else {
                productItem.quantity -= qty;
                tempCart.push({
                    ...inventory_product,
                    item_quantity: qty
                })
    
                this.setState({
                    cart : tempCart,
                    products: tempProducts
                })
            }

            this.setState({
                products : tempProducts
            })
        } 
    }
    handleChangeQty(payload, quantity){
        const { cart,products } = this.state;
        let tempCart = [...cart];
        let tempProducts = [...products];
        let productItem = tempProducts.find(item => item.id == payload.id );
        tempCart.find((item,index) => {
            if(item.id == payload.id){
                let qty = _.parseInt(tempCart[index].item_quantity) + _.parseInt(quantity);
                let qtyProduct =  quantity < 0 ? productItem.quantity - _.parseInt(quantity) : productItem.quantity - _.parseInt(quantity);
                if(qty != 0){
                    productItem.quantity = qtyProduct;
                    tempCart[index].item_quantity = qty;
                    tempCart[index].quantity = qtyProduct;
                   
                    this.setState({
                        cart : tempCart,
                        products: tempProducts
                    })
                } else {
                    productItem.quantity = qtyProduct;
                    tempCart.push({
                        ...payload,
                        item_quantity: qty,
                        products: tempProducts
                    })

                    this.setState({
                        cart : tempCart
                   })
                  
                }
            }
        })
    }

    getCartToLocalStorage(){
        return JSON.parse(localStorage.getItem('cart')) || [];
    }

    handleRemoveCartItem(payload){  
        let tempCart = [...this.state.cart];
        let tempProducts = [...this.state.products];
        let productItem = tempProducts.find(item => item.id == payload.id );
        const index = tempCart.indexOf(payload);
        productItem.quantity += tempCart[index].item_quantity;
        tempCart.splice(index, 1);

        this.setState({
            cart: tempCart,
            products: tempProducts
        });
    }

    handleSubmitOrder()
    {   
        const { cart,customer_point_info,discount, customerFullName, cash_tendered, customer_point_earner} = this.state;
        let getTotalAmountDue = this.getTotalAmountDue();
        console.log("Cash Tendered", cash_tendered);
        console.log("Total Amount Due", getTotalAmountDue);
        if(cash_tendered < getTotalAmountDue){
            Swal.fire("Error!", "Please check your cash tendered amount", "error");
        } else {
            http.post('/pos', {cart, customer_point_info, discount , customerFullName, cash_tendered, customer_point_earner}).then(res => {
                if(res.data.status === "success"){
                    const sales = res.data.sales;
                    let a= document.createElement('a');
                    a.target= '_blank';
                    a.href= "/invoice/" + sales.id; 
                    a.click();
        
                    this.handleResetCart();
                }
            }).catch(err => {
                Swal.fire("Error!", err.response.data.message, "error");
            })
        }
    }

    handleResetCart(){
        const { 
            cart,
            customer_point_info,
            discount,
            customer_point_earner
        } = this.state;
        let tempCart = [...cart];
        let tempCustomerPointInfo = {...customer_point_info};
        let tempCustomerEarnerInfo = {...customer_point_earner};
        let tempDiscount = {...discount};
        tempCart = [];
        tempCustomerPointInfo = {};
        tempCustomerEarnerInfo = {};
        tempDiscount = [];

        this.setState({
            cart: tempCart,    
            customer_point_info: tempCustomerPointInfo,
            customer_point_earner: tempCustomerEarnerInfo,
            customer_earner_balance: "",
            discount: tempDiscount,
            item_quantity: "",
            cash_tendered: "",
            selectedNormalDiscount: false,
            selectedCustomerDiscount: false,
            customerFullName: ""
        })        
    }

    render() {
        const { 
            cart, 
            products, 
            item_quantity, 
            normal_discounts, 
            cash_tendered,
            point_discount, 
            selectedCustomerDiscount, 
            selectedNormalDiscount, 
            customerFullName,
            customerReferenceNo,
            customer_point_info,
            customer_earner_balance,
            customer_point_earner
        } = this.state;

        const options = products && products.map(inventory =>{
            return { label: `${inventory.product_name} - ₱${inventory.selling_price}`, value: inventory}
        });

        let cashTendered = cart.length != 0 ? false: true;

        let getTotalAmountDue = this.getTotalAmountDue().toFixed(2);

        let cashChange = cash_tendered != "" ? cash_tendered - getTotalAmountDue : 0;

        console.log("Custoemr Info",customer_point_info)
        console.log("Cart info",cart)
       
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-md-8">
                        <div className="card">
                            <div className="card-body">
                                {/* <div className="form-group row">
                                    <label className="col-lg-3 col-form-label">Customer Name:</label>
                                    <div className="col-lg-9">	
                                        <input 
                                            type="text"
                                            className="form-control" placeholder="e.g Juan Dela Cruz" 
                                            value={customerFullName} 
                                            onChange={this.setCustomerFullName} 
                                        />
                                    </div>
                                </div> */}
                                <div className="form-group row">
                                    <label className="col-lg-4 col-form-label">Customer Reference No:</label>
                                    <div className="col-lg-6">	
                                        <input 
                                            type="text"
                                            className="form-control" placeholder="e.g 9890368664 (Optional)" 
                                            value={customerReferenceNo} 
                                            onChange={this.setCustomerReferenceNo} 
                                        />
                                    </div>
                                    <div className="col-lg-2">
                                        <button type="submit" onClick={this.handleCheckCustomer} className="btn btn-primary"><i className="fa fa-search"></i></button>
                                    </div>	
                                </div>
                                { Object.keys(customer_point_earner).length > 0 && (
                                    <React.Fragment>
                                    {/* <div className="form-group">
                                        <label className="col-lg-3 col-form-label">Reference No:</label>
                                        <label className="col-lg-9 col-form-label">{customer_point_earner.reference_no}</label>
                                    </div> */}
                                    <div className="form-group">
                                        <label className="col-lg-3 col-form-label">Fullname:</label>
                                        <label className="col-lg-9 col-form-label">{customer_point_earner.name}</label>
                                    </div>
                                    <div className="form-group">
                                        <label className="col-lg-3 col-form-label">Balance:</label>
                                        <label className="col-lg-9 col-form-label">{customer_earner_balance} points</label>
                                    </div>
                                    </React.Fragment>
                                )}
                                <div className="form-group row">
                                    <label className="col-lg-12 col-form-label">PURCHASE ITEMS:</label>
                                </div>
                                <div className="form-group row">
                                    <div className="col-6">
                                        <Select 
                                            placeholder='Select Product...'
                                            isSearchable
                                            value={options.label}
                                            options={options}
                                            onChange={(option) => this.setInventoryId(option.value) }
                                        />
                                    </div>
                                    <div className="col-3">	
                                        <input 
                                            type="number" 
                                            value={item_quantity} 
                                            onChange={this.setQuantity} 
                                            className="form-control" 
                                            placeholder="Quantity" 
                                        />
                                    </div>
                                    <div className="col-3">	
                                        <button type="submit" onClick={this.handleAddToCart} className="btn btn-primary"><i className="fa fa-shopping-cart"></i></button>
                                    </div>
                                </div>
                                <div className="row">
                                <div className="col-md-12">
                                    <table className="table table-bordered">
                                        <thead>
                                            <tr  style={{
                                                textAlign:"center"
                                            }}>
                                                <th>ITEM NAME</th>
                                                <th>PRICE PER UNIT</th>
                                                <th>QTY</th>
                                                <th>TOTAL PRICE</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {cart.length != 0 ? 
                                            cart.map(c => (
                                                <tr key={c.id}>
                                                    <td>{c.product_name}</td>
                                                    <td style={{
                                                textAlign:"right"
                                            }}>₱{c.selling_price}</td>
                                                    <td style={{
                                                textAlign:"right"
                                            }}>
                                                        {c.item_quantity}
                                                        <button 
                                                            className="btn bg-gradient-danger btn-sm mr-2 ml-2"
                                                            disabled={c.item_quantity <= 1}
                                                            onClick={() => this.handleChangeQty(c, -1)}
                                                        ><i className="fa fa-minus"></i></button>
                                                        <button 
                                                            className="btn bg-gradient-danger btn-sm"
                                                            disabled={c.quantity == 0}
                                                            onClick={() => this.handleChangeQty(c, 1)}
                                                        ><i className="fa fa-plus"></i></button>
                                                    </td>
                                                    <td style={{
                                                textAlign:"right"
                                            }}>
                                                        ₱{(
                                                            c.selling_price * c.item_quantity
                                                        ).toFixed(2)
                                                        }
                                                    </td>
                                                    <td>
                                                        <button 
                                                            className="btn bg-gradient-danger btn-sm"
                                                            onClick={() => this.handleRemoveCartItem(c)}
                                                        ><i className="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>

                                            )) : 
                                            <React.Fragment>
                                                <tr>
                                                    <td colSpan="5" style={{textAlign: "center"}}>No Data Available</td>
                                                </tr>
                                            </React.Fragment>

                                            }
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-4">
                        <div className="card">
                            <div className="card-body">
                                <div className="form-group row">
                                    <label className="col-form-label">TRANSACTION</label>
                                </div>
                                <div className="form-group row">
                                    <label className="col-lg-6 col-form-label">TOTAL:</label>
                                    <label className="col-lg-6 col-form-label">₱{ this.getTotal().toFixed(2) }</label>
                                </div>
                                <div className="form-group row">
                                    <label className="col-lg-12 col-form-label">DISCOUNT:</label>
                                </div>
                                <div className="form-group row">
                                    <div className="col-lg-6">
                                        {point_discount.map(discount => (
                                            <div className="custom-control custom-radio" key={discount.id}>
                                                <input
                                                    className="custom-control-input"
                                                    type="radio"
                                                    name={discount.point_name}
                                                    value={discount.id}
                                                    onChange={this.handleSelectCustomerPoint}
                                                    id="customerPoint"
                                                    checked={selectedCustomerDiscount}
                                                />
                                                <label htmlFor="customerPoint" className="custom-control-label">{discount.point_name}</label>
                                            </div>
                                        ))}
                                    </div>
                                    <div className="col-lg-6">
                                        {normal_discounts.map(discount => (
                                            <div className="custom-control custom-radio" key={discount.id}>
                                                <input
                                                    type="radio"
                                                    className="custom-control-input"
                                                    name={discount}
                                                    value={discount.id}
                                                    onChange={this.handleSelectNormalDiscount}
                                                    id={discount.id}
                                                    checked={selectedNormalDiscount}
                                                />
                                                <label htmlFor={discount.id} className="custom-control-label">{discount.discount_name}</label>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                                { Object.keys(customer_point_info).length > 0 && (
                                    <React.Fragment>
                                    <div className="form-group row">
                                        <label className="col-lg-6 col-form-label">Customer Ref No:</label>
                                        <label className="col-lg-6 col-form-label">{customer_point_info.reference_no}</label>
                                    </div>
                                    <div className="form-group row">
                                        <label className="col-lg-6 col-form-label">Customer Name:</label>
                                        <label className="col-lg-6 col-form-label">{customer_point_info.name}</label>
                                    </div>
                                    </React.Fragment>
                                )}
                                <div className="form-group row">
                                    <label className="col-lg-6 col-form-label">TOTAL DISCOUNT:</label>
                                    <label className="col-lg-6 col-form-label">₱{this.getTotalDiscount()}</label>
                                </div>
                                <div className="form-group row">
                                    <label className="col-lg-6 col-form-label">AMOUNT DUE:</label>
                                    <label className="col-lg-6 col-form-label">₱{getTotalAmountDue}</label>
                                </div>
                                <div className="form-group row">
									<label className="col-lg-6 col-form-label">CASH TENDERED:</label>
									<div className="col-lg-6">	
										<input type="text" value={cash_tendered} className="form-control" onChange={this.setCashTendered} placeholder="Cash Tendered" disabled={cashTendered}/>
									</div>
								</div>
                                <div className="form-group row">
                                    <label className="col-lg-6 col-form-label">CHANGE:</label>
                                    <label className="col-lg-6 col-form-label">₱{cashChange.toFixed(2)}</label>
                                </div>
                                <div className="form-group row">
                                    <div className="col-lg-12">
                                        <button type="button" onClick={this.handleSubmitOrder} className="btn btn-success btn-block" disabled={cashTendered}><i className="fa fa-bell"></i> COMPLETE ORDER (F5)</button>
                                    </div>
                                </div>
                                <div className="form-group row">
                                    <div className="col-lg-12">
                                        <button type="button" onClick={this.handleResetCart} className="btn btn-warning btn-block" disabled={cashTendered}><i className="fa fa-bell"></i> CANCEL ORDER (F6)</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default Cart;

if (document.getElementById('cart')) {
    ReactDOM.render(<Cart />, document.getElementById('cart'));
}
