import React, { Component } from 'react'
import ReactDOM from 'react-dom'

class App extends Component {
  constructor(props){
    super(props);
    const root = document.querySelector("#root")
    this.props = {...(root.dataset)}
    console.log(this.props)
    this.state = {
      entries: [],
    }
  }
  componentDidMount(){
    fetch('/data')
      .then(response => response.json())
      .then(entries => {
        this.setState({
          entries
        })
      })
  }
  render() {
    const {allProduct} = this.props
    return (
      <div>
          {allProduct}
          {/* id : {allProduct.id}
          marque : {allProduct.marque}
          model : {allProduct.model} */}
        {/* {allProduct.map(entry =>
          <div>
            id : {entry.id}
            marque : {entry.marque}
            model : {entry.model}
          </div>
        )} */}
      </div>
    );
  }
}

ReactDOM.render(<App/>, document.querySelector("#root"))