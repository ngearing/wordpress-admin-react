import React from 'react'
import ReactDOM from 'react-dom'

class App extends React.Component {
    render() {
        return (
            <div className="wrap">
                <h1>My App is Awesome</h1>
                dd
            </div>
        )
    }
}

ReactDOM.render(<App />, document.querySelector('#app-root'))
