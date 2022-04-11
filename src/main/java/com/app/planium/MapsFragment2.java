package com.app.planium;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.app.planium.adapter.PlanListAdapter;
import com.app.planium.app.App;
import com.app.planium.constants.Constants;
import com.app.planium.model.Plan;
import com.app.planium.util.CustomRequest;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapView;
import com.google.android.gms.maps.MapsInitializer;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.MarkerOptions;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

public class MapsFragment2 extends Fragment implements Constants, OnMapReadyCallback {

    private static final String STATE_LIST = "State Adapter Data";
    private static MapsFragment2 INSTANCE = null;
    private GoogleMap mMap;
    SwipeRefreshLayout mItemsContainer;

    private ArrayList<Plan> itemsList;
    private PlanListAdapter itemsAdapter;
    private Boolean loadingMore = false;

    private long planId = 0;
    private float lat = 0;
    private float lng = 0;

    private int itemId = 0;
    private int arrayLength = 0;
    private Boolean viewMore = false;
    private Boolean restore = false;
    private Boolean loaded = false;
    private Boolean pager = false;
    View view;
    com.google.android.gms.maps.MapView MapView;
    MapView mapView;
    public MapsFragment2() {
        // Required empty public constructor
    }

    public static MapsFragment2 getInstance() {
        if( INSTANCE == null)
            INSTANCE = new MapsFragment2();
        return INSTANCE;
    }
    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setHasOptionsMenu(false);

        if (savedInstanceState != null) {

            itemsList = savedInstanceState.getParcelableArrayList(STATE_LIST);
            itemsAdapter = new PlanListAdapter(getActivity(), itemsList);

            viewMore = savedInstanceState.getBoolean("viewMore");
            restore = savedInstanceState.getBoolean("restore");
            loaded = savedInstanceState.getBoolean("loaded");
            pager = savedInstanceState.getBoolean("pager");
            itemId = savedInstanceState.getInt("itemId");

        } else {

            itemsList = new ArrayList<Plan>();
            itemsAdapter = new PlanListAdapter(getActivity(), itemsList);

            restore = false;
            loaded = false;
            pager = false;
            itemId = 0;
        }

        Intent i = getActivity().getIntent();

        planId = i.getLongExtra("planId", 0);

        if (planId == 0) planId = App.getInstance().getId();

        if (getArguments() != null) {

            pager = getArguments().getBoolean("pager", false);
        }
    }

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        view = inflater.inflate(R.layout.activity_maps2, container, false);

        SupportMapFragment mapFragment = (SupportMapFragment) getChildFragmentManager().findFragmentById(R.id.map);
        mapFragment.getMapAsync(this);
        return view;
    }

    @Override
    public void onMapReady(GoogleMap googleMap) {
        MapsInitializer.initialize(getContext());
        mMap = googleMap;



        // REQUEST
        CustomRequest jsonReq = new CustomRequest(Request.Method.POST, METHOD_GET_PLANS, null,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {

                        if (!isAdded() || getActivity() == null) {

                            Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                            return;
                        }

                        if (!loadingMore) {

                            itemsList.clear();
                        }

                        try {

                            arrayLength = 0;

                            if (!response.getBoolean("error")) {

                                itemId = response.getInt("itemId");

                                if (response.has("items")) {

                                    JSONArray usersArray = response.getJSONArray("items");
                                    arrayLength = usersArray.length();
                                    //Log.e("IdBasedatos: ", String.valueOf(arrayLength));
                                    //Log.e("IdBasedatos: ", String.valueOf(usersArray));
                                    if (arrayLength > 0) {

                                        for (int i = 0; i < usersArray.length(); i++) {

                                            JSONObject userObj = (JSONObject) usersArray.get(i);
                                            //Log.e("LatLng: ", String.valueOf(userObj.get("lat"))+ ", "+String.valueOf(userObj.get("lng")));
                                            Plan plan = new Plan(userObj);
                                            itemsList.add(plan);
                                            //Log.e("id perfil: ", String.valueOf(itemsList.getDistance()));
                                            Log.e("id perfil: ", String.valueOf(plan.getDistance()));
                                            LatLng mipos = new LatLng(plan.getLat(), plan.getLng());
                                            mMap.addMarker(new MarkerOptions().position(mipos).title(plan.getUsername()).icon(BitmapDescriptorFactory.fromResource(R.drawable.ic_launcher_planeator_45)));
                                        }
                                    }
                                }

                            }

                        } catch (JSONException e) {

                            e.printStackTrace();

                        } finally {

                            if (!isAdded() || getActivity() == null) {

                                Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                                return;
                            }


                            Log.e("Response", response.toString());
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {

            }
        }) {

            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<String, String>();
                params.put("accountId", Long.toString(App.getInstance().getId()));
                params.put("accessToken", App.getInstance().getAccessToken());
                params.put("itemId", Long.toString(itemId));
                params.put("lat", Double.toString(App.getInstance().getLat()));
                params.put("lng", Double.toString(App.getInstance().getLng()));
                return params;
            }
        };

        App.getInstance().addToRequestQueue(jsonReq);
        //


        LatLng sydney = new LatLng(-34, 151);
        LatLng mipos = new LatLng(App.getInstance().getLat(), App.getInstance().getLng());
        mMap.addMarker(new MarkerOptions().position(mipos).title("Aquí estoy"));
        mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(mipos,8));
    }
    public void getItems() {

        Log.e("Lat & Lng", Double.toString(App.getInstance().getLat()) + " & " + Double.toString(App.getInstance().getLng()));


        CustomRequest jsonReq = new CustomRequest(Request.Method.POST, METHOD_GET_PLANS, null,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {

                        if (!isAdded() || getActivity() == null) {

                            Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                            return;
                        }

                        if (!loadingMore) {

                            itemsList.clear();
                        }

                        try {

                            arrayLength = 0;

                            if (!response.getBoolean("error")) {

                                itemId = response.getInt("itemId");

                                if (response.has("items")) {

                                    JSONArray usersArray = response.getJSONArray("items");
                                    arrayLength = usersArray.length();
                                    //Log.e("IdBasedatos: ", String.valueOf(arrayLength));
                                    //Log.e("IdBasedatos: ", String.valueOf(usersArray));
                                    if (arrayLength > 0) {

                                        for (int i = 0; i < usersArray.length(); i++) {

                                            JSONObject userObj = (JSONObject) usersArray.get(i);
                                            Log.e("LatLng: ", String.valueOf(userObj.get("lat"))+ ", "+String.valueOf(userObj.get("lng")));
                                            //Log.e("Longg: ", String.valueOf(userObj));
                                            Plan plan = new Plan(userObj);
                                            itemsList.add(plan);
                                            //Log.e("id perfil: ", String.valueOf(itemsList.getDistance()));
                                            //Log.e("id perfil: ", String.valueOf(plan.getDistance()));
                                        }
                                    }
                                }

                            }

                        } catch (JSONException e) {

                            e.printStackTrace();

                        } finally {

                            if (!isAdded() || getActivity() == null) {

                                Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                                return;
                            }


                            Log.e("Response", response.toString());
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {

            }
        }) {

            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<String, String>();
                params.put("accountId", Long.toString(App.getInstance().getId()));
                params.put("accessToken", App.getInstance().getAccessToken());
                params.put("itemId", Long.toString(itemId));
                params.put("lat", Double.toString(App.getInstance().getLat()));
                params.put("lng", Double.toString(App.getInstance().getLng()));
                return params;
            }
        };

        App.getInstance().addToRequestQueue(jsonReq);
    }




    @Override
    public void onAttach(Activity activity) {
        super.onAttach(activity);
    }

    @Override
    public void onDetach() {
        super.onDetach();
    }
}