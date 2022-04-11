package com.app.planium;

import android.Manifest;
import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Location;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Bundle;
import android.provider.Settings;
import android.util.Log;
import android.view.GestureDetector;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.app.planium.adapter.PlanListAdapter;
import com.app.planium.app.App;
import com.app.planium.constants.Constants;
import com.app.planium.model.Plan;
import com.app.planium.util.CustomRequest;
import com.app.planium.util.Helper;
import com.balysv.materialripple.MaterialRippleLayout;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.MapView;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.google.android.material.snackbar.Snackbar;
import com.squareup.picasso.Callback;
import com.squareup.picasso.Picasso;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import androidx.annotation.NonNull;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;


public class PlanMapsFragment extends Fragment implements Constants, SwipeRefreshLayout.OnRefreshListener {

    private static final String STATE_LIST = "State Adapter Data";
    Spinner mGroupCategory, mDistance, mAdicional, mCuando, mOrdenar;
    private FusedLocationProviderClient mFusedLocationClient;
    private Location mLastLocation;

    private Context mContext;
    public TextView mProfileFullname, mProfileUsername;
    public ImageView mProfilePhoto, mProfileOnlineIcon, mProfileIcon;
    public MaterialRippleLayout mParent;
    public ProgressBar mProgressBar;

    Menu MainMenu;
    FloatingActionButton mapaFabButton, settingsbuttom;
    RecyclerView mRecyclerView;
    TextView mMessage, mDetails;
    Marker marker;
    SwipeRefreshLayout mItemsContainer;

    LinearLayout mSpotLight, mPermissionSpotlight;

    Button mGrantPermission, mIr;

    private ArrayList<Plan> itemsListt;
    private PlanListAdapter itemsAdapter;

    private int itemId = 0;
    private int arrayLength = 0;
    private Boolean restore = false;
    private Boolean spotlight = true;
    private int distance = 50, distancia_elegida= 0, categoria_elegida= 0;      // im miles
    MapView mMapView;
    private GoogleMap googleMap;

    int pastVisiblesItems = 0, visibleItemCount = 0, totalItemCount = 0;

    public PlanMapsFragment() {
        // Required empty public constructor
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);

//        setRetainInstance(true);

        setHasOptionsMenu(true);

        if (savedInstanceState != null) {

            itemsListt = savedInstanceState.getParcelableArrayList(STATE_LIST);
            itemsAdapter = new PlanListAdapter(getActivity(), itemsListt);

            restore = savedInstanceState.getBoolean("restore");
            spotlight = savedInstanceState.getBoolean("spotlight");
            itemId = savedInstanceState.getInt("itemId");
            distance = savedInstanceState.getInt("distance");

        } else {

            itemsListt = new ArrayList<Plan>();
            itemsAdapter = new PlanListAdapter(getActivity(), itemsListt);

            restore = false;
            spotlight = true;
            itemId = 0;
            distance = 50;
        }
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

        View rootView = inflater.inflate(R.layout.fragment_mapa_plan, container, false);
        mMapView = (MapView) rootView.findViewById(R.id.mapView);
        mMapView.onCreate(savedInstanceState);

        mMapView.onResume(); // needed to get the map to display immediately


        mSpotLight = (LinearLayout) rootView.findViewById(R.id.spotlight);
        mDetails = (TextView) rootView.findViewById(R.id.openLocationSettings);

        mPermissionSpotlight = (LinearLayout) rootView.findViewById(R.id.permission_spotlight);
        mGrantPermission = (Button) rootView.findViewById(R.id.grantPermissionBtn);

        final GridLayoutManager mLayoutManager = new GridLayoutManager(getActivity(), Helper.getGridSpanCount(getActivity()));


        mGrantPermission.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {

                if (ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {

                    if (ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), Manifest.permission.ACCESS_COARSE_LOCATION) || ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION)){

                        requestPermissions(new String[]{Manifest.permission.ACCESS_COARSE_LOCATION, Manifest.permission.ACCESS_FINE_LOCATION}, MY_PERMISSIONS_REQUEST_ACCESS_LOCATION);

                    } else {

                        requestPermissions(new String[]{Manifest.permission.ACCESS_COARSE_LOCATION, Manifest.permission.ACCESS_FINE_LOCATION}, MY_PERMISSIONS_REQUEST_ACCESS_LOCATION);
                    }
                }
            }
        });


        mDetails.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Intent i = new Intent(getActivity(), LocationActivity.class);
                startActivityForResult(i, 101);
            }
        });

        updateSpotLight();

        if (!restore && App.getInstance().getLat() != 0.000000 && App.getInstance().getLng() != 0.000000) {

            if (ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {

                updateSpotLight();

            } else {


                getItems2(distancia_elegida, categoria_elegida);
            }
        }
        mRecyclerView = (RecyclerView) rootView.findViewById(R.id.recycler_view);


        settingsbuttom = (FloatingActionButton) rootView.findViewById(R.id.settingsButton);
        settingsbuttom.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                android.app.AlertDialog.Builder b = new android.app.AlertDialog.Builder(getActivity());

                /** Setting a title for the window */
                b.setTitle("Filtrar por");

                LinearLayout view = (LinearLayout) getActivity().getLayoutInflater().inflate(R.layout.dialog_filter, null);

                b.setView(view);

                mAdicional = (Spinner) view.findViewById(R.id.adicional);
                mCuando = (Spinner) view.findViewById(R.id.cuando);
                mGroupCategory = (Spinner) view.findViewById(R.id.category);
                mOrdenar = (Spinner) view.findViewById(R.id.ordenar);

                ArrayAdapter<String> categoria = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, android.R.id.text1);
                categoria.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                mGroupCategory.setAdapter(categoria);
                categoria.add("Todas");
                categoria.add(getString(R.string.group_category_1));
                categoria.add(getString(R.string.group_category_2));
                categoria.add(getString(R.string.group_category_3));
                categoria.add(getString(R.string.group_category_4));
                categoria.add(getString(R.string.group_category_5));
                categoria.notifyDataSetChanged();

                mGroupCategory.setSelection(0);

                ArrayAdapter<String> ordenar = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, android.R.id.text1);
                ordenar.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                mOrdenar.setAdapter(ordenar);
                ordenar.add("Cercanía");
                ordenar.add("Popularidad");
                ordenar.add("Promocion");
                ordenar.notifyDataSetChanged();
                mOrdenar.setSelection(0);

                ArrayAdapter<String> cuando = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, android.R.id.text1);
                cuando.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                mCuando.setAdapter(cuando);
                cuando.add("Siempre");
                cuando.add("Esta semana");
                cuando.notifyDataSetChanged();
                mCuando.setSelection(0);

                ArrayAdapter<String> adicional = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, android.R.id.text1);
                adicional.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                mAdicional.setAdapter(adicional);
                adicional.add("Nada");
                adicional.add("Con promoción");
                adicional.add("Verificados");
                adicional.notifyDataSetChanged();
                mAdicional.setSelection(0);

                mDistance = (Spinner) view.findViewById(R.id.distance);

                ArrayAdapter<String> distancia = new ArrayAdapter<String>(getActivity(), android.R.layout.simple_spinner_item, android.R.id.text1);
                distancia.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                mDistance.setAdapter(distancia);
                distancia.add("Todas");
                distancia.add(getString(R.string.dialog_nearby_1));
                distancia.add(getString(R.string.dialog_nearby_2));
                distancia.add(getString(R.string.dialog_nearby_3));
                distancia.add(getString(R.string.dialog_nearby_4));
                distancia.notifyDataSetChanged();

                mDistance.setSelection(0);

                /** Setting a positive button and its listener */

                b.setCancelable(true);

                b.setNegativeButton(getText(R.string.action_cancel), new DialogInterface.OnClickListener() {

                    @Override
                    public void onClick(DialogInterface dialog, int which) {

                        dialog.cancel();
                    }
                });
                b.setPositiveButton(getText(R.string.action_ok), new DialogInterface.OnClickListener() {

                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        itemId = 0;
                        distancia_elegida = mDistance.getSelectedItemPosition();
                        categoria_elegida = mGroupCategory.getSelectedItemPosition();
                        getItems2(distancia_elegida, categoria_elegida);
                    }
                });
                b.show();

            }
        });


        mapaFabButton = (FloatingActionButton) rootView.findViewById(R.id.fabbButton);
        mapaFabButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                Intent i = new Intent(getActivity(), NewPlanActivity.class);
                startActivity(i);


            }
        });
        getItems2(distancia_elegida, categoria_elegida);

        // Inflate the layout for this fragment
        return rootView;
    }

    public void updateSpotLight() {

        if (ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED && ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {

            if (ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), Manifest.permission.ACCESS_COARSE_LOCATION) || ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION)){

                showPermissionSpotlight();
                hideNoLocationSpotlight();

            } else {

                showPermissionSpotlight();
                hideNoLocationSpotlight();
            }

        } else {

            hidePermissionSpotlight();

            if (App.getInstance().getLat() != 0.000000 && App.getInstance().getLng() != 0.000000) {

                hidePermissionSpotlight();
                hideNoLocationSpotlight();

            } else {

                showNoLocationSpotlight();
            }
        }

        getActivity().invalidateOptionsMenu();
    }



    public void showPermissionSpotlight() {

        mPermissionSpotlight.setVisibility(View.VISIBLE);
    }

    public void showNoLocationSpotlight() {

        mSpotLight.setVisibility(View.VISIBLE);
    }

    public void hidePermissionSpotlight() {

        mPermissionSpotlight.setVisibility(View.GONE);
    }

    public void hideNoLocationSpotlight() {

        mSpotLight.setVisibility(View.GONE);
    }

    public void updateItems() {

        if (App.getInstance().getLat() != 0.000000 && App.getInstance().getLng() != 0.000000) {


            itemId = 0;

            getItems2(distancia_elegida, categoria_elegida);
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {

        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == 101 && resultCode == getActivity().RESULT_OK) {

            updateSpotLight();

            updateItems();

        } else if (requestCode == 10001 && resultCode == getActivity().RESULT_OK) {

            updateSpotLight();

            updateItems();
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String permissions[], int[] grantResults) {

        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        switch (requestCode) {

            case MY_PERMISSIONS_REQUEST_ACCESS_LOCATION: {

                // If request is cancelled, the result arrays are empty.

                if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {

                    LocationManager lm = (LocationManager) getActivity().getSystemService(getActivity().LOCATION_SERVICE);

                    if (lm.isProviderEnabled(LocationManager.GPS_PROVIDER) && ContextCompat.checkSelfPermission(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED) {

                        mFusedLocationClient = LocationServices.getFusedLocationProviderClient(getActivity());

                        mFusedLocationClient.getLastLocation().addOnCompleteListener(getActivity(), new OnCompleteListener<Location>() {
                            @Override
                            public void onComplete(@NonNull Task<Location> task) {

                                if (task.isSuccessful() && task.getResult() != null) {

                                    mLastLocation = task.getResult();

                                    Log.d("GPS", "WelcomeActivity onComplete" + Double.toString(mLastLocation.getLatitude()));
                                    Log.d("GPS", "WelcomeActivity onComplete" + Double.toString(mLastLocation.getLongitude()));

                                    App.getInstance().setLat(mLastLocation.getLatitude());
                                    App.getInstance().setLng(mLastLocation.getLongitude());

                                } else {

                                    Log.d("GPS", "WelcomeActivity getLastLocation:exception", task.getException());
                                }

                                updateSpotLight();

                                updateItems();
                            }
                        });
                    }

                    updateSpotLight();

                } else if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_DENIED) {

                    if (!ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), Manifest.permission.ACCESS_COARSE_LOCATION) || !ActivityCompat.shouldShowRequestPermissionRationale(getActivity(), Manifest.permission.ACCESS_FINE_LOCATION)) {

                        showNoLocationPermissionSnackbar();
                    }
                }

                return;
            }
        }
    }

    public void showNoLocationPermissionSnackbar() {

        Snackbar.make(getView(), getString(R.string.label_no_location_permission) , Snackbar.LENGTH_LONG).setAction(getString(R.string.action_settings), new View.OnClickListener() {

            @Override
            public void onClick(View v) {

                openApplicationSettings();

                Toast.makeText(getActivity(), getString(R.string.label_grant_location_permission), Toast.LENGTH_SHORT).show();

            }

        }).show();
    }

    public void openApplicationSettings() {

        Intent appSettingsIntent = new Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS, Uri.parse("package:" + getActivity().getPackageName()));
        startActivityForResult(appSettingsIntent, 10001);
    }

    @Override
    public void onStart() {

        super.onStart();

        updateSpotLight();
    }

    @Override
    public void onRefresh() {

        if (App.getInstance().isConnected()) {

            itemId = 0;

            getItems(distancia_elegida);

        } else {

            mItemsContainer.setRefreshing(false);
        }
    }

    @Override
    public void onSaveInstanceState(Bundle outState) {

        super.onSaveInstanceState(outState);
        outState.putBoolean("restore", true);
        outState.putBoolean("spotlight", spotlight);
        outState.putInt("itemId", itemId);
        outState.putInt("distance", distance);
        outState.putParcelableArrayList(STATE_LIST, itemsListt);
    }

    public void getItems(long distancia) {
        itemsListt = new ArrayList<Plan>();
        Log.e("Lat & Lng", Double.toString(App.getInstance().getLat()) + " & " + Double.toString(App.getInstance().getLng()));

        //Log.e("Disst: ", String.valueOf(distancia));
        mMapView.getMapAsync(new OnMapReadyCallback() {
            @Override
            public void onMapReady(GoogleMap mMap) {
                googleMap = mMap;
                googleMap.clear();
                googleMap.setMyLocationEnabled(true);

                // For showing a move to my location button
                CustomRequest jsonReq = new CustomRequest(Request.Method.POST, METHOD_GET_PLANS2, null,
                        new Response.Listener<JSONObject>() {
                            @Override
                            public void onResponse(JSONObject response) {

                                if (!isAdded() || getActivity() == null) {

                                    Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                                    return;
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
                                                    itemsListt.add(plan);
                                                    //Log.e("id perfil: ", String.valueOf(plan.getDistance()));
                                                    LatLng mipos = new LatLng(plan.getLat(), plan.getLng());
                                                    //Log.e("id perfil: ", String.valueOf(mipos));
                                                    marker= mMap.addMarker(new MarkerOptions().position(mipos).title(String.valueOf(i)).snippet("Distancia: " +plan.getDistance()).icon(BitmapDescriptorFactory.fromResource(R.drawable.ic_launcher_planeator_45)));

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
                        params.put("distance", Integer.toString(distance));
                        params.put("itemId", Long.toString(itemId));
                        params.put("dist", Long.toString(distancia));
                        params.put("lat", Double.toString(App.getInstance().getLat()));
                        params.put("lng", Double.toString(App.getInstance().getLng()));
                        return params;
                    }
                };


                App.getInstance().addToRequestQueue(jsonReq);
                //


                LatLng mipos = new LatLng(App.getInstance().getLat(), App.getInstance().getLng());
                //mMap.addMarker(new MarkerOptions().position(mipos).title("Aquí estoy"));
                mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(mipos,8));
                googleMap.setOnMarkerClickListener(new GoogleMap.OnMarkerClickListener() {
                    @Override
                    public boolean onMarkerClick(Marker marker) {
                        String title = marker.getTitle();
                        int posicion = Integer.parseInt(title);
                        Log.e("Title: ", String.valueOf(posicion));
                        android.app.AlertDialog.Builder m = new android.app.AlertDialog.Builder(getActivity());

                        /** Setting a title for the window */
                        //m.setTitle(title);

                        RelativeLayout view = (RelativeLayout) getActivity().getLayoutInflater().inflate(R.layout.plan_thumbnail, null);

                        m.setView(view);

                        mParent = (MaterialRippleLayout) view.findViewById(R.id.parent);

                        mProfilePhoto = (ImageView) view.findViewById(R.id.profileImg);
                        mProfileFullname = (TextView) view.findViewById(R.id.profileFullname);
                        mProfileUsername = (TextView) view.findViewById(R.id.profileUsername);
                        mProfileOnlineIcon = (ImageView) view.findViewById(R.id.profileOnlineIcon);
                        mProfileIcon = (ImageView) view.findViewById(R.id.profileIcon);
                        mProgressBar = (ProgressBar) view.findViewById(R.id.progressBar);
                        mIr = (Button) view.findViewById(R.id.irrr);
                        Plan item = itemsListt.get(posicion);

                        mProgressBar.setVisibility(View.VISIBLE);
                        mProfilePhoto.setVisibility(View.GONE);

                        if (item.getNormalPhotoUrl() != null && item.getNormalPhotoUrl().length() > 0) {

                            final ImageView img = mProfilePhoto;
                            final ProgressBar progressView = mProgressBar;

                            Picasso.with(mContext)
                                    .load(item.getNormalPhotoUrl())
                                    .into(mProfilePhoto, new Callback() {

                                        @Override
                                        public void onSuccess() {

                                            progressView.setVisibility(View.GONE);
                                            img.setVisibility(View.VISIBLE);
                                        }

                                        @Override
                                        public void onError() {

                                            progressView.setVisibility(View.GONE);
                                            img.setVisibility(View.VISIBLE);
                                            img.setImageResource(R.drawable.profile_default_photo);
                                        }
                                    });

                        } else {

                            mProgressBar.setVisibility(View.GONE);
                            mProfilePhoto.setVisibility(View.VISIBLE);

                            mProfilePhoto.setImageResource(R.drawable.profile_default_photo);
                        }
                        mProfileFullname.setText(item.getFullname());

                        mProfileUsername.setText(Double.toString(item.getDistance()) + "km" + " @" + item.getUsername());

                        if (item.isOnline()) {

                            mProfileOnlineIcon.setVisibility(View.VISIBLE);

                        } else {

                            mProfileOnlineIcon.setVisibility(View.GONE);
                        }

                        if (item.isVerify()) {

                            mProfileIcon.setVisibility(View.VISIBLE);

                        } else {

                            mProfileIcon.setVisibility(View.GONE);
                        }
                        /*mProfilePhoto.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                Intent intent = new Intent(getActivity(), PlanActivity.class);
                                intent.putExtra("groupId", item.getId());
                                startActivity(intent);
                            }
                        });*/
                        mIr.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                Intent intent = new Intent(getActivity(), PlanActivity.class);
                                intent.putExtra("groupId", item.getId());
                                startActivity(intent);
                            }
                        });


                        m.setCancelable(true);
                        /*m.setNegativeButton(getText(R.string.action_cancel), new DialogInterface.OnClickListener() {

                            @Override
                            public void onClick(DialogInterface dialog, int which) {

                                dialog.cancel();
                            }
                        });
                        m.setPositiveButton(getText(R.string.action_ok), new DialogInterface.OnClickListener() {

                            @Override
                            public void onClick(DialogInterface dialog, int which) {
                                Intent intent = new Intent(getActivity(), PlanActivity.class);
                                intent.putExtra("groupId", item.getId());
                                startActivity(intent);
                            }
                        });*/

                        m.show();
                        return true;

                    }
                });
            }


        });

    }
    public void getItems2(long distancia, int categoria) {
        itemsListt = new ArrayList<Plan>();
        Log.e("Lat & Lng", Double.toString(App.getInstance().getLat()) + " & " + Double.toString(App.getInstance().getLng()));

        //Log.e("Disst: ", String.valueOf(distancia));
        mMapView.getMapAsync(new OnMapReadyCallback() {
            @Override
            public void onMapReady(GoogleMap mMap) {
                googleMap = mMap;
                googleMap.clear();
                googleMap.setMyLocationEnabled(true);

                // For showing a move to my location button
                CustomRequest jsonReq = new CustomRequest(Request.Method.POST, METHOD_GET_PLANS2, null,
                        new Response.Listener<JSONObject>() {
                            @Override
                            public void onResponse(JSONObject response) {

                                if (!isAdded() || getActivity() == null) {

                                    Log.e("ERROR", "PeopleNearbyFragment Not Added to Activity");

                                    return;
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
                                                    itemsListt.add(plan);
                                                    //Log.e("id perfil: ", String.valueOf(plan.getDistance()));
                                                    LatLng mipos = new LatLng(plan.getLat(), plan.getLng());
                                                    //Log.e("id perfil: ", String.valueOf(mipos));
                                                    marker= mMap.addMarker(new MarkerOptions().position(mipos).title(String.valueOf(i)).snippet("Distancia: " +plan.getDistance()).icon(BitmapDescriptorFactory.fromResource(R.drawable.ic_launcher_planeator_45)));

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
                        params.put("distance", Integer.toString(distance));
                        params.put("categoria", Integer.toString(categoria));
                        params.put("itemId", Long.toString(itemId));
                        params.put("dist", Long.toString(distancia));
                        params.put("lat", Double.toString(App.getInstance().getLat()));
                        params.put("lng", Double.toString(App.getInstance().getLng()));
                        return params;
                    }
                };


                App.getInstance().addToRequestQueue(jsonReq);
                //


                LatLng mipos = new LatLng(App.getInstance().getLat(), App.getInstance().getLng());
                //mMap.addMarker(new MarkerOptions().position(mipos).title("Aquí estoy"));
                mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(mipos,8));
                googleMap.setOnMarkerClickListener(new GoogleMap.OnMarkerClickListener() {
                    @Override
                    public boolean onMarkerClick(Marker marker) {
                        String title = marker.getTitle();
                        int posicion = Integer.parseInt(title);
                        Log.e("Title: ", String.valueOf(posicion));
                        android.app.AlertDialog.Builder m = new android.app.AlertDialog.Builder(getActivity());

                        /** Setting a title for the window */
                        //m.setTitle(title);

                        RelativeLayout view = (RelativeLayout) getActivity().getLayoutInflater().inflate(R.layout.plan_thumbnail, null);

                        m.setView(view);

                        mParent = (MaterialRippleLayout) view.findViewById(R.id.parent);

                        mProfilePhoto = (ImageView) view.findViewById(R.id.profileImg);
                        mProfileFullname = (TextView) view.findViewById(R.id.profileFullname);
                        mProfileUsername = (TextView) view.findViewById(R.id.profileUsername);
                        mProfileOnlineIcon = (ImageView) view.findViewById(R.id.profileOnlineIcon);
                        mProfileIcon = (ImageView) view.findViewById(R.id.profileIcon);
                        mProgressBar = (ProgressBar) view.findViewById(R.id.progressBar);
                        mIr = (Button) view.findViewById(R.id.irrr);
                        Plan item = itemsListt.get(posicion);

                        mProgressBar.setVisibility(View.VISIBLE);
                        mProfilePhoto.setVisibility(View.GONE);

                        if (item.getNormalPhotoUrl() != null && item.getNormalPhotoUrl().length() > 0) {

                            final ImageView img = mProfilePhoto;
                            final ProgressBar progressView = mProgressBar;

                            Picasso.with(mContext)
                                    .load(item.getNormalPhotoUrl())
                                    .into(mProfilePhoto, new Callback() {

                                        @Override
                                        public void onSuccess() {

                                            progressView.setVisibility(View.GONE);
                                            img.setVisibility(View.VISIBLE);
                                        }

                                        @Override
                                        public void onError() {

                                            progressView.setVisibility(View.GONE);
                                            img.setVisibility(View.VISIBLE);
                                            img.setImageResource(R.drawable.profile_default_photo);
                                        }
                                    });

                        } else {

                            mProgressBar.setVisibility(View.GONE);
                            mProfilePhoto.setVisibility(View.VISIBLE);

                            mProfilePhoto.setImageResource(R.drawable.profile_default_photo);
                        }
                        mProfileFullname.setText(item.getFullname());

                        mProfileUsername.setText(Double.toString(item.getDistance()) + "km" + " @" + item.getUsername());

                        if (item.isOnline()) {

                            mProfileOnlineIcon.setVisibility(View.VISIBLE);

                        } else {

                            mProfileOnlineIcon.setVisibility(View.GONE);
                        }

                        if (item.isVerify()) {

                            mProfileIcon.setVisibility(View.VISIBLE);

                        } else {

                            mProfileIcon.setVisibility(View.GONE);
                        }
                        /*mProfilePhoto.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                Intent intent = new Intent(getActivity(), PlanActivity.class);
                                intent.putExtra("groupId", item.getId());
                                startActivity(intent);
                            }
                        });*/
                        mIr.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View v) {
                                Intent intent = new Intent(getActivity(), PlanActivity.class);
                                intent.putExtra("groupId", item.getId());
                                startActivity(intent);
                            }
                        });


                        m.setCancelable(true);
                        /*m.setNegativeButton(getText(R.string.action_cancel), new DialogInterface.OnClickListener() {

                            @Override
                            public void onClick(DialogInterface dialog, int which) {

                                dialog.cancel();
                            }
                        });
                        m.setPositiveButton(getText(R.string.action_ok), new DialogInterface.OnClickListener() {

                            @Override
                            public void onClick(DialogInterface dialog, int which) {
                                Intent intent = new Intent(getActivity(), PlanActivity.class);
                                intent.putExtra("groupId", item.getId());
                                startActivity(intent);
                            }
                        });*/

                        m.show();
                        return true;

                    }
                });
            }


        });

    }



    public void loadingComplete() {


    }

    static class RecyclerItemClickListener implements RecyclerView.OnItemTouchListener {

        public interface OnItemClickListener {

            void onItemClick(View view, int position);

            void onItemLongClick(View view, int position);
        }

        private OnItemClickListener mListener;

        private GestureDetector mGestureDetector;



        @Override
        public boolean onInterceptTouchEvent(RecyclerView view, MotionEvent e) {

            View childView = view.findChildViewUnder(e.getX(), e.getY());

            if (childView != null && mListener != null && mGestureDetector.onTouchEvent(e)) {

                mListener.onItemClick(childView, view.getChildAdapterPosition(childView));
            }

            return false;
        }

        @Override
        public void onTouchEvent(RecyclerView view, MotionEvent motionEvent) {

        }

        @Override
        public void onRequestDisallowInterceptTouchEvent(boolean disallowIntercept) {

        }
    }



    private int setDistanceChecked(int miles) {

        int result = 0;

        switch (miles) {

            case 50: {

                result = 0;

                break;
            }

            case 100: {

                result = 1;

                break;
            }

            case 250: {

                result = 2;

                break;
            }

            case 500: {

                result = 3;

                break;
            }

            case 1000: {

                result = 4;

                break;
            }

            default: {

                result = 0;

                break;
            }
        }

        return  result;
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