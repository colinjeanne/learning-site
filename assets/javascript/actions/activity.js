import Constants from '../constants/constants';
import { createAction } from 'redux-actions';
import {
    getRequestAction,
    postRequestAction,
    putRequestAction } from './requestHelpers';

export const getMyActivities =
    () => getRequestAction(
        '/me/family/activities',
        Constants.GET_MY_ACTIVITIES);

export const addActivity =
    (name, description) => postRequestAction(
        '/me/family/activities',
        Constants.ADD_ACTIVITY,
        {
            name,
            description
        });

export const getActivity =
    activity => getRequestAction(
        activity.links.self,
        Constants.GET_ACTIVITY);
        
export const updateActivity =
    activity => putRequestAction(
        activity.links.self,
        Constants.UPDATE_ACTIVITY,
        activity);