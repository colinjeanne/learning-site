import { createAction } from 'redux-actions';

const getGetHeaders = idToken => {
    let headers = {
        Accept: 'application/json'
    };

    if (idToken) {
        // If there is no idToken then we are using cookies
        headers['Authorization'] = `Bearer ${idToken}`;
    }

    return new Headers(headers);
};

const getPostOrPutHeaders = idToken =>
    new Headers({
        'Authorization': `Bearer ${idToken}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    });

const getIdTokenFromState = state => state.user.idToken;

export const getRequestAction = (uri, actionType) =>
    (dispatch, getState) => {
        const action = createAction(actionType);
        dispatch(action());

        const idToken = getIdTokenFromState(getState());
        return fetch(
            uri,
            {
                credentials: 'same-origin',
                method: 'GET',
                headers: getGetHeaders(idToken)
            }).
            then(response => {
                if (!response.ok) {
                    throw new Error(`Request failed: ${response.status}`);
                }

                return response.json();
            }).
            then(json => dispatch(action(json))).
            catch(err => dispatch({
                type: actionType,
                payload: err,
                error: true
            }));
    };

export const postRequestAction = (uri, actionType, payload) =>
    (dispatch, getState) => {
        dispatch({
            type: actionType,
            payload: payload,
            meta: {
                volatile: true
            }
        });

        const idToken = getIdTokenFromState(getState());
        const postPayload = {
            method: 'POST',
            headers: getPostOrPutHeaders(idToken)
        };

        if (payload) {
            postPayload.body = JSON.stringify(payload);
        }

        return fetch(uri, postPayload).
            then(response => {
                if (!response.ok) {
                    throw new Error(`Request failed: ${response.status}`);
                }

                return response.json();
            }).
            then(json => dispatch({
                type: actionType,
                payload: json
            })).
            catch(err => dispatch({
                type: actionType,
                payload: err,
                error: true,
                meta: {
                    payload: payload
                }
            }));
    };

export const putRequestAction = (uri, actionType, payload) =>
    (dispatch, getState) => {
        dispatch({
            type: actionType,
            payload: payload,
            meta: {
                volatile: true
            }
        });

        const idToken = getIdTokenFromState(getState());
        return fetch(
            uri,
            {
                method: 'PUT',
                headers: getPostOrPutHeaders(idToken),
                body: JSON.stringify(payload)
            }).
            then(response => {
                if (!response.ok) {
                    throw new Error(`Request failed: ${response.status}`);
                }

                return response.json();
            }).
            then(json => dispatch({
                type: actionType,
                payload: json
            })).
            catch(err => dispatch({
                type: actionType,
                payload: err,
                error: true,
                meta: {
                    payload: payload
                }
            }));
    };